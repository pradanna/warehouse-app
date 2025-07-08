<?php

namespace App\Services\InventoryMovement;

use App\Schemas\InventoryMovement\InventoryMovementQuery;
use App\Commons\Http\ServiceResponse;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;

class InventoryMovementService implements InventoryMovementServiceInterface
{
    public function findAll(InventoryMovementQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = InventoryMovement::with([
                'inventory.item',
                'inventory.unit',
                'author'
            ])->orderBy('created_at', 'ASC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get inventory movements", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage() . $e->getLine());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $inventoryMovement = InventoryMovement::with([
                'inventory.item',
                'inventory.unit',
                'author'
            ])
                ->where('id', '=', $id)
                ->first();
            if (!$inventoryMovement) {
                return ServiceResponse::notFound("inventory movement not found");
            }
            return ServiceResponse::statusOK("successfully get inventory movement", $inventoryMovement);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function summary(InventoryMovementQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $data = DB::table('inventories as i')
                ->leftJoin('inventory_movements as im', function ($join) use ($queryParams) {
                    $join->on('i.id', '=', 'im.inventory_id')
                        ->whereBetween(DB::raw('DATE(im.created_at)'), [
                            $queryParams->getDateStart(),
                            $queryParams->getDateEnd()
                        ]);
                })
                ->join('items as it', 'i.item_id', '=', 'it.id')
                ->select(
                    'i.id as id',
                    'it.name as name',
                    DB::raw('DATE(im.created_at) as date'),
                    DB::raw("MIN(im.quantity_open) as quantity_open"),
                    DB::raw("SUM(CASE WHEN im.type = 'in' THEN im.quantity ELSE 0 END) as quantity_in"),
                    DB::raw("SUM(CASE WHEN im.type = 'out' THEN im.quantity ELSE 0 END) as quantity_out")
                )
                ->groupBy(
                    'i.id',
                    'it.name',
                    DB::raw('DATE(im.created_at)')
                )
                ->orderBy('date', 'ASC')
                ->get()
                ->groupBy('id')
                ->map(function ($items, $id) {
                    $firstItem = $items->first();
                    return (object) [
                        'id' => $id,
                        'name' => $firstItem->name,
                        'movements' => $items->filter(fn($item) => $item->date !== null)->map(function ($item) {
                            return (object) [
                                'date' => $item->date,
                                'in' => $item->quantity_in,
                                'out' => $item->quantity_out,
                                'open' => $item->quantity_open,
                            ];
                        })->values()
                    ];
                })->values();
            return ServiceResponse::statusOK("successfully get inventory movement", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
