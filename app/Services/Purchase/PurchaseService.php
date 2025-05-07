<?php

namespace App\Services\Purchase;

use App\Schemas\Purchase\PurchaseSchema;
use App\Commons\Http\ServiceResponse;
use App\Commons\Pagination\Pagination;
use App\Models\Purchase;
use App\Schemas\Purchase\PurchaseQuery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseService implements PurchaseServiceInterface
{
    public function create(PurchaseSchema $schema): ServiceResponse
    {
        try {
            DB::beginTransaction();
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray());
            }
            $schema->hydrateBody();
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
                'payment_status' => $schema->getPaymentStatus()
            ];
            $purchase = Purchase::create($data);
            $items = $schema->getItems();
            // $payment = $schema->getPayment();
            $payment['author_id'] = Auth::user()->id;
            $purchase->items()->createMany($items);
            // $purchase->payment()->create($payment);
            DB::commit();
            return ServiceResponse::statusCreated("successfully create purchase");
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
                'supplier:id,name,contact,address',
                'items.item:id,purchase_id,',
                'items.unit'
            ])
                ->orderBy('date', 'DESC');
            $pagination = new Pagination();
            $pagination->setQuery($query)
                ->setPage($queryParams->getPage())
                ->setPerPage($queryParams->getPerPage())
                ->paginate();
            $data = $pagination->getData()->makeHidden([
                'created_at',
                'updated_at',
                'supplier_id',
                'items.purchase_id'
            ]);
            $meta = $pagination->getJsonMeta();
            return ServiceResponse::statusOK("successfully get purchases", $data, $meta);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findByID($id): ServiceResponse
    {
        try {
            $item = Purchase::with([])
                ->where('id', '=', $id)
                ->first();
            if (!$item) {
                return ServiceResponse::notFound("purchase not found");
            }
            $item->makeHidden(['created_at', 'updated_at']);
            return ServiceResponse::statusOK("successfully get purchase", $item);
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }
}
