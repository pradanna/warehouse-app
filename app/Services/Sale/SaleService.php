<?php

namespace App\Services\Sale;

use App\Commons\Enum\InventoryMovementType;
use App\Commons\Http\HttpStatus;
use App\Commons\Http\ServiceResponse;
use App\Http\Resources\Sale\SaleCollection;
use App\Http\Resources\Sale\SaleResource;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Sale;
use App\Schemas\Sale\SaleQuery;
use App\Schemas\Sale\SaleSchema;
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
            $payment['author_id'] = $userId;
            $itemCollections = collect($items);
            $subTotal = $itemCollections->sum('total');
            $total = $subTotal + $schema->getTax() - $schema->getDiscount();
            $paymentStatus = 'paid';
            if ($schema->getPaymentType() === 'cash' && ($payment['amount'] !== $total)) {
                return ServiceResponse::badRequest("bad request (Payment mismatch: the amount paid does not correspond to the total required.)");
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
            $sale->payment()->create($payment);
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
            $sale->load([
                'outlet',
                'items.inventory',
                'payments',
                'author'
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
            $queryParams->hydrateQuery();
            $query = Sale::with([
                'outlet',
                'items.inventory',
                'payments',
                'author'
            ])
                ->orderBy('date', 'DESC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
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
                'payments'
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
}
