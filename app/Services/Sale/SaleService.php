<?php

namespace App\Services\Sale;

use App\Commons\Http\HttpStatus;
use App\Http\Resources\Sale\SaleCollection;
use App\Http\Resources\Sale\SaleResource;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Sale;
use App\Schemas\Sale\SaleQuery;
use App\Schemas\Sale\SaleSchema;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleService implements SaleServiceInterface
{
    public function create(SaleSchema $schema): Responsable
    {
        try {
            DB::beginTransaction();
            $validator = $schema->validate();
            if ($validator->fails()) {
                return (new SaleResource(null))
                    ->additional(['errors' => $validator->errors()->toArray()])
                    ->withStatus(HttpStatus::UnprocessableEntity)
                    ->withMessage("error validation");
            }
            $schema->hydrateBody();
            $items = $schema->getItems();
            $payment = $schema->getPayment();
            $payment['author_id'] = Auth::user()->id;
            $itemCollections = collect($items);
            $subTotal = $itemCollections->sum(function ($item) {
                return $item['quantity'] * $item['price'];
            });
            $total = $subTotal + $schema->getTax() - $schema->getDiscount();
            $paymentStatus = 'paid';
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
                'payment_status' => $paymentStatus
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
                    return (new SaleResource(null))
                        ->withStatus(HttpStatus::NotFound)
                        ->withMessage("inventory not found");
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
                    'movement_type' => 'purchase',
                    'movement_reference' => $sale->id
                ];
                InventoryMovement::create($movementData);
            }
            DB::commit();
            return (new SaleResource(null))
                ->withStatus(HttpStatus::Created)
                ->withMessage("successfully create sale");
        } catch (\Throwable $e) {
            DB::rollBack();
            return (new SaleResource(null))
                ->withMessage($e->getMessage());
        }
    }

    public function findAll(SaleQuery $queryParams): Responsable
    {
        try {
            $queryParams->hydrateQuery();
            $query = Sale::with([
                'outlet',
                'items.inventory',
                'payments'
            ])
                ->orderBy('date', 'DESC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return (new SaleCollection($data))
                ->withStatus(HttpStatus::OK)
                ->withMessage('successfully retrieved sales');
        } catch (\Throwable $e) {
            return (new SaleResource(null))
                ->withStatus(HttpStatus::InternalServerError)
                ->withMessage($e->getMessage());
        }
    }

    public function findByID($id): Responsable
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
                return (new SaleResource(null))
                    ->withStatus(HttpStatus::NotFound)
                    ->withMessage("purchase not found");
            }
            return (new SaleResource($sale))
                ->withStatus(HttpStatus::OK)
                ->withMessage("successfully retrieved purchase");
        } catch (\Throwable $e) {
            return (new SaleResource(null))
                ->withStatus(HttpStatus::InternalServerError)
                ->withMessage($e->getMessage());
        }
    }
}
