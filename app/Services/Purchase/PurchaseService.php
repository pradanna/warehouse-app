<?php

namespace App\Services\Purchase;

use App\Commons\Enum\InventoryMovementType;
use App\Commons\Enum\PurchasePaymentStatus;
use App\Commons\Enum\PurchasePaymentType;
use App\Commons\Http\HttpStatus;
use App\Schemas\Purchase\PurchaseSchema;
use App\Commons\Http\ServiceResponse;
use App\Commons\Pagination\Pagination;
use App\Http\Resources\Purchase\PurchaseCollection;
use App\Http\Resources\Purchase\PurchaseResource;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Purchase;
use App\Schemas\Purchase\PurchasePaymentSchema;
use App\Schemas\Purchase\PurchaseQuery;
use App\Services\Inventory\InventoryService;
use Illuminate\Contracts\Support\Responsable;
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
            $purchase->load([
                'supplier',
                'items.inventory',
                'payments',
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
            $queryParams->hydrateQuery();
            $query = Purchase::with([
                'supplier',
                'items.inventory',
                'payments',
                'author'
            ])
                ->orderBy('date', 'DESC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
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
}
