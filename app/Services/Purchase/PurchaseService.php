<?php

namespace App\Services\Purchase;

use App\Commons\Http\HttpStatus;
use App\Schemas\Purchase\PurchaseSchema;
use App\Commons\Http\ServiceResponse;
use App\Commons\Pagination\Pagination;
use App\Http\Resources\Purchase\PurchaseCollection;
use App\Http\Resources\Purchase\PurchaseResource;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Purchase;
use App\Schemas\Purchase\PurchaseQuery;
use App\Services\Inventory\InventoryService;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseService implements PurchaseServiceInterface
{
    public function create(PurchaseSchema $schema): Responsable
    {
        try {
            DB::beginTransaction();
            $validator = $schema->validate();
            if ($validator->fails()) {
                return (new PurchaseResource(null))
                    ->additional(['errors' => $validator->errors()->toArray()])
                    ->withStatus(HttpStatus::UnprocessableEntity)
                    ->withMessage("error validation");
            }
            $schema->hydrateBody();
            $items = $schema->getItems();
            $payment = $schema->getPayment();
            $payment['author_id'] = Auth::user()->id;

            $paymentStatus = 'paid';
            $data = [
                'supplier_id' => $schema->getSupplierId(),
                'date' => $schema->getDate(),
                'reference_number' => $schema->getReferenceNumber(),
                'sub_total' => $schema->getSubTotal(),
                'discount' => $schema->getDiscount(),
                'tax' => $schema->getTax(),
                'total' => $schema->getTotal(),
                'description' => $schema->getDescription(),
                'payment_type' => $schema->getPaymentType(),
                'payment_status' => $paymentStatus
            ];
            $purchase = Purchase::create($data);
            $purchase->items()->createMany($items);
            $purchase->payment()->create($payment);
            foreach ($items as $item) {
                $inventory = Inventory::with([])
                    ->where('id', '=', $item['inventory_id'])
                    ->first();
                if (!$inventory) {
                    DB::rollBack();
                    return (new PurchaseResource(null))
                        ->withStatus(HttpStatus::NotFound)
                        ->withMessage("inventory not found");
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
                    'movement_type' => 'purchase',
                    'movement_reference' => $purchase->id
                ];
                InventoryMovement::create($movementData);
            }
            DB::commit();
            return (new PurchaseResource(null))
                ->withStatus(HttpStatus::Created)
                ->withMessage("successfully create purchase");
        } catch (\Throwable $e) {
            DB::rollBack();
            return (new PurchaseResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function findAll(PurchaseQuery $queryParams): Responsable
    {
        try {
            $queryParams->hydrateQuery();
            $query = Purchase::with([
                'supplier',
                'items.inventory',
                'payments'
            ])
                ->orderBy('date', 'DESC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return (new PurchaseCollection($data))
                ->withStatus(HttpStatus::OK)
                ->withMessage('successfully retrieved purchases');
        } catch (\Throwable $e) {
            return (new PurchaseResource(null))
                ->withStatus(HttpStatus::InternalServerError)
                ->withMessage($e->getMessage());
        }
    }

    public function findByID($id): Responsable
    {
        try {
            $purchase = Purchase::with([
                'supplier',
                'items.inventory',
                'payments'
            ])
                ->where('id', '=', $id)
                ->first();
            if (!$purchase) {
                return (new PurchaseResource(null))
                    ->withStatus(HttpStatus::NotFound)
                    ->withMessage("purchase not found");
            }
            return (new PurchaseResource($purchase))
                ->withStatus(HttpStatus::OK)
                ->withMessage("successfully retrieved purchase");
        } catch (\Throwable $e) {
            return (new PurchaseResource(null))
                ->withStatus(HttpStatus::InternalServerError)
                ->withMessage($e->getMessage());
        }
    }

    private function patchInventory($items)
    {
        try {
            foreach ($items as $item) {
                $inventory = Inventory::where('item_id', $item['item_id'])
                    ->where('unit_id', '=', $item['unit_id'])
                    ->first();
                if (!$inventory) {
                    return [
                        'success' => false,
                        'message' => 'inventory not found'
                    ];
                }
                $currentStock = $inventory->current_stock;
                $newStock = $currentStock + $item['quantity'];
                $inventory->update([
                    'current_stock' => $newStock
                ]);
            }
            return [
                'success' => true,
                'message' => 'successfully patch stock'
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
