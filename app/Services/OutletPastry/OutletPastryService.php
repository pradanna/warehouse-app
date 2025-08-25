<?php

namespace App\Services\OutletPastry;

use App\Schemas\OutletPastry\OutletPastrySchema;
use App\Commons\Http\ServiceResponse;
use App\Models\OutletPastry;
use App\Models\OutletPastryItem;
use App\Schemas\OutletPastry\OutletPastryQuery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OutletPastryService implements OutletPastryServiceInterface
{
    public function findAll(OutletPastryQuery $queryParams): ServiceResponse
    {
        try {
            $queryParams->hydrateQuery();
            $query = OutletPastry::with([
                'outlet',
                'items',
                'author'
            ])
                ->when($queryParams->getOutletId(), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->where('outlet_id', '=', $queryParams->getOutletId());
                })
                ->when(($queryParams->getDateStart() && $queryParams->getDateEnd()), function ($q) use ($queryParams) {
                    /** @var Builder $q */
                    return $q->whereBetween('date', [$queryParams->getDateStart(), $queryParams->getDateEnd()]);
                })
                ->orderBy('created_at', 'ASC');
            $data = $query->paginate($queryParams->getPerPage(), '*', 'page', $queryParams->getPage());
            return ServiceResponse::statusOK("successfully get outlet pastries", $data);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage() . $e->getLine());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $outletPastry = OutletPastry::with([
                'outlet',
                'items',
                'author'
            ])
                ->where('id', '=', $id)
                ->first();
            if (!$outletPastry) {
                return ServiceResponse::notFound("outlet pastry not found");
            }
            return ServiceResponse::statusOK("successfully get outlet pastry", $outletPastry);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function create(OutletPastrySchema $schema): ServiceResponse
    {
        try {
            $userId = Auth::user()->id;
            DB::beginTransaction();
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();

            $carts = $schema->getCarts();
            $total = collect($carts)->sum(function ($item) {
                return $item['qty'] * $item['price'];
            });
            $dataPastry = [
                'outlet_id' => $schema->getOutletId(),
                'date' => $schema->getDate(),
                'reference_number' => $schema->getReferenceNumber(),
                'sub_total' => $total,
                'discount' => 0,
                'total' => $total,
                'author_id' => $userId,
            ];
            $outletPastry = OutletPastry::create($dataPastry);

            foreach ($carts as $cart) {
                $item = [
                    'outlet_pastry_id' => $outletPastry->id,
                    'name' => $cart['name'],
                    'quantity' => $cart['qty'],
                    'price' => $cart['price'],
                    'total' => ($cart['qty'] * $cart['price']),
                ];
                OutletPastryItem::create($item);
            }
            DB::commit();
            return ServiceResponse::statusCreated("successfully create outlet pastry");
        } catch (\Throwable $e) {
            DB::rollBack();
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
