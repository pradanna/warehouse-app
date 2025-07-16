<?php

namespace App\Services\Sale;

use App\Commons\Enum\CashFlowType;
use App\Commons\Enum\InventoryMovementType;
use App\Commons\Enum\SalePaymentStatus;
use App\Commons\Enum\SalePaymentType;
use App\Commons\Http\HttpStatus;
use App\Commons\Http\ServiceResponse;
use App\Http\Resources\Sale\SaleCollection;
use App\Http\Resources\Sale\SaleResource;
use App\Models\CashFlow;
use App\Models\Credit;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Sale;
use App\Schemas\Sale\SaleQuery;
use App\Schemas\Sale\SaleSchema;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleService implements SaleServiceInterface
{
    public function create(SaleSchema $schema): ServiceResponse
    {
        try {
            $userId = Auth::user()->id;
            DB::beginTransaction();
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();
            $items = $schema->getItems();
            $payment = $schema->getPayment();

            $itemCollections = collect($items);
            $subTotal = $itemCollections->sum('total');
            $total = $subTotal + $schema->getTax() - $schema->getDiscount();
            $paymentStatus = SalePaymentStatus::Unpaid->value;

            # create data payment if payment exist
            if ($payment) {
                $payment['author_id'] = $userId;
                if ($schema->getPaymentType() === SalePaymentType::Cash->value) {
                    $paymentStatus = SalePaymentStatus::Paid->value;
                    if ($payment['amount'] !== $total) {
                        return ServiceResponse::badRequest("bad request (Payment mismatch: the amount paid does not correspond to the total required.)");
                    }
                } else {
                    $paymentStatus = SalePaymentStatus::Partial->value;
                }
            }

            $data = [
                'outlet_id' => $schema->getOutletId(),
                'date' => $schema->getDate(),
                'reference_number' => $schema->getReferenceNumber(),
                'sub_total' => $subTotal,
                'discount' => $schema->getDiscount(),
                'tax' => $schema->getTax(),
                'total' => $total,
                'description' => $schema->getDescription(),
                'payment_type' => $schema->getPaymentType(),
                'payment_status' => $paymentStatus,
                'author_id' => $userId
            ];
            $sale = Sale::create($data);
            $sale->items()->createMany($items);

            if ($payment) {
                $sale->payment()->create($payment);
            }

            # update inventory stock and create inventory movements
            foreach ($items as $item) {
                $inventory = Inventory::with([])
                    ->where('id', '=', $item['inventory_id'])
                    ->first();
                if (!$inventory) {
                    DB::rollBack();
                    return ServiceResponse::notFound("inventory not found");
                }
                $currentStock = $inventory->current_stock;
                $newStock = $currentStock - $item['quantity'];
                $inventory->update(['current_stock' => $newStock]);

                $movementData = [
                    'inventory_id' => $item['inventory_id'],
                    'type' => 'out',
                    'quantity_open' => $currentStock,
                    'quantity' => $item['quantity'],
                    'quantity_close' => $newStock,
                    'description' => 'Purchasing',
                    'movement_type' => InventoryMovementType::Sale->value,
                    'movement_reference' => $sale->id,
                    'author_id' => $userId
                ];
                InventoryMovement::create($movementData);
            }

            # create credit record if payment type is installment
            if ($schema->getPaymentType() === SalePaymentType::Installment->value) {
                $dataCredit = [
                    'sale_id' => $sale->id,
                    'amount_due' => $total,
                    'amount_paid' => 0,
                    'amount_rest' => $total,
                    'due_date' => null
                ];
                if ($payment) {
                    $dataCredit['amount_paid'] = $payment['amount'];
                    $dataCredit['amount_rest'] = $total - $payment['amount'];
                }
                Credit::create($dataCredit);
            }

            #create cash flows
            Carbon::setLocale('id');
            $formattedDate = Carbon::parse($schema->getDate())->translatedFormat('d F Y');
            $dataCashFlow = [
                'outlet_id' => $schema->getOutletId(),
                'date' => $schema->getDate(),
                'type' => CashFlowType::Credit->value,
                'name' => 'Purchase ' . $formattedDate,
                'amount' => $total,
                'description' => null,
                'author_id' => $userId,
            ];
            CashFlow::create($dataCashFlow);

            $sale->load([
                'outlet',
                'items.inventory',
                'payments',
                'author',
                'credit'
            ]);
            DB::commit();
            return ServiceResponse::statusCreated("successfully create sale", $sale);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findAll(SaleQuery $queryParams): ServiceResponse
    {
        try {
            $data = $this->saleQuery($queryParams)
                ->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get sales", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $sale = Sale::with([
                'outlet',
                'items.inventory',
                'payments',
                'credit',
                'author'
            ])
                ->where('id', '=', $id)
                ->first();
            if (!$sale) {
                return ServiceResponse::notFound("sale not found");
            }
            return ServiceResponse::statusOK("successfully get sale", $sale);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function summary(SaleQuery $queryParams): ServiceResponse
    {
        try {
            $total = $this->saleQuery($queryParams)
                ->sum('total');
            return ServiceResponse::statusOK("successfully get sale summary", $total);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    private function saleQuery(SaleQuery $queryParams): Builder
    {
        $queryParams->hydrateQuery();
        return Sale::with([
            'outlet',
            'items.inventory',
            'payments',
            'credit',
            'author'
        ])
            ->when($queryParams->getParam(), function ($q) use ($queryParams) {
                /** @var Builder $q */
                return $q->where('reference_number', 'LIKE', "%{$queryParams->getParam()}%");
            })
            ->when(($queryParams->getDateStart() && $queryParams->getDateEnd()), function ($q) use ($queryParams) {
                /** @var Builder $q */
                return $q->whereBetween('date', [$queryParams->getDateStart(), $queryParams->getDateEnd()]);
            })
            ->when($queryParams->getOutletId(), function ($q) use ($queryParams) {
                /** @var Builder $q */
                return $q->where('outlet_id', '=', $queryParams->getOutletId());
            })
            ->when($queryParams->getType(), function ($q) use ($queryParams) {
                /** @var Builder $q */
                return $q->where('payment_type', '=', $queryParams->getType());
            })
            ->when($queryParams->getStatus(), function ($q) use ($queryParams) {
                /** @var Builder $q */
                return $q->where('payment_status', '=', $queryParams->getStatus());
            })
            ->orderBy('date', 'DESC');
    }
}
