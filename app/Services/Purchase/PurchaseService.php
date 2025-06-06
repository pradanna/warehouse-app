<?php

namespace App\Services\Purchase;

use App\Commons\Enum\InventoryMovementType;
use App\Commons\Enum\PurchasePaymentStatus;
use App\Commons\Enum\PurchasePaymentType;
use App\Schemas\Purchase\PurchaseSchema;
use App\Commons\Http\ServiceResponse;
use App\Models\Debt;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Purchase;
use App\Schemas\Purchase\PurchaseQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseService implements PurchaseServiceInterface
{
    public function create(PurchaseSchema $schema): ServiceResponse
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
            $paymentStatus = PurchasePaymentStatus::Unpaid->value;
            if ($payment) {
                $payment['author_id'] = $userId;
                if ($schema->getPaymentType() === PurchasePaymentType::Cash->value) {
                    $paymentStatus = PurchasePaymentStatus::Paid->value;
                    if ($payment['amount'] !== $total) {
                        return ServiceResponse::badRequest("bad request (Payment mismatch: the amount paid does not correspond to the total required.)");
                    }
                } else {
                    $paymentStatus = PurchasePaymentStatus::Partial->value;
                }
            }

            $data = [
                'supplier_id' => $schema->getSupplierId(),
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
            $purchase = Purchase::create($data);
            $purchase->items()->createMany($items);

            if ($payment) {
                $purchase->payment()->create($payment);
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
                $newStock = $currentStock + $item['quantity'];
                $inventory->update(['current_stock' => $newStock]);

                $movementData = [
                    'inventory_id' => $item['inventory_id'],
                    'type' => 'in',
                    'quantity_open' => $currentStock,
                    'quantity' => $item['quantity'],
                    'quantity_close' => $newStock,
                    'description' => 'Purchasing',
                    'movement_type' => InventoryMovementType::Purhcase->value,
                    'movement_reference' => $purchase->id,
                    'author_id' => $userId
                ];
                InventoryMovement::create($movementData);
            }

            # create debt record if payment type is installment
            if ($schema->getPaymentType() === PurchasePaymentType::Installment->value) {
                $dataDebt = [
                    'purchase_id' => $purchase->id,
                    'amount_due' => $total,
                    'amount_paid' => 0,
                    'amount_rest' => $total,
                    'due_date' => null
                ];
                if ($payment) {
                    $dataDebt['amount_paid'] = $payment['amount'];
                    $dataDebt['amount_rest'] = $total - $payment['amount'];
                }
                Debt::create($dataDebt);
            }

            $purchase->load([
                'supplier',
                'items.inventory',
                'payments',
                'debt',
                'author'
            ]);
            DB::commit();
            return ServiceResponse::statusCreated("successfully create purchase", $purchase);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findAll(PurchaseQuery $queryParams): ServiceResponse
    {
        try {
            $data = $this->purchaseQuery($queryParams)
                ->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get purchases", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $purchase = Purchase::with([
                'supplier',
                'items.inventory',
                'payments',
                'debt',
                'author'
            ])
                ->where('id', '=', $id)
                ->first();
            if (!$purchase) {
                return ServiceResponse::notFound("purchase not found");
            }
            return ServiceResponse::statusOK("successfully get purchase", $purchase);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function summary(PurchaseQuery $queryParams): ServiceResponse
    {
        try {
            $total = $this->purchaseQuery($queryParams)
                ->sum('total');
            return ServiceResponse::statusOK("successfully get purchase summary", $total);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    private function purchaseQuery(PurchaseQuery $queryParams): Builder
    {
        $queryParams->hydrateQuery();
        return Purchase::with([
            'supplier',
            'items.inventory',
            'payments',
            'debt',
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
            ->when($queryParams->getSupplierId(), function ($q) use ($queryParams) {
                /** @var Builder $q */
                return $q->where('supplier_id', '=', $queryParams->getSupplierId());
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
