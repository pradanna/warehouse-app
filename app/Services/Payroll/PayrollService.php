<?php

namespace App\Services\Payroll;

use App\Schemas\Payroll\PayrollSchema;
use App\Commons\Http\ServiceResponse;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Schemas\Payroll\PayrollQuery;

class PayrollService implements PayrollServiceInterface
{
    public function create(PayrollSchema $schema): ServiceResponse
    {
        try {
            $validator = $schema->validate();
            if ($validator->fails()) {
                return ServiceResponse::unprocessableEntity($validator->errors()->toArray(), "error validation");
            }
            $schema->hydrateBody();
            $items = $schema->getItems();

            $itemCollections = collect($items);

            $total = $itemCollections->sum('amount');

            $data = [
                'outlet_id' => $schema->getOutletId(),
                'date' => $schema->getDate(),
                'amount' => $total,
            ];

            $payroll = Payroll::create($data);

            foreach ($items as $item) {
                $dataItem = [
                    'payroll_id' => $payroll->id,
                    'employee_id' => $item['employee_id'],
                    'amount' => $item['amount']
                ];
                PayrollItem::create($dataItem);
            }

            return ServiceResponse::statusCreated("successfully create payroll");
        } catch (\Throwable $e) {
            return ServiceResponse::internalServerError($e->getMessage());
        }
    }

    public function findAll(PayrollQuery $queryParams): ServiceResponse {}

    public function findByID($id): ServiceResponse {}

    public function update($id, PayrollSchema $schema): ServiceResponse {}

    public function delete($id): ServiceResponse {}
}
