<?php

namespace App\Schemas\Payroll;

use App\Commons\Schema\BaseSchema;

class PayrollSchema extends BaseSchema
{
    private $outletId;
    private $date;
    private $items;

    protected function rules()
    {
        return [
            'outlet_id' => 'required|uuid',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.employee_id' => 'required|uuid',
            'items.*.amount' => 'required|numeric',
        ];
    }

    public function hydrateBody()
    {
        $outletId = $this->body['outlet_id'];
        $date = $this->body['date'];
        $items = $this->body['items'];

        $this->setOutletId($outletId)
            ->setDate($date)
            ->setItems($items);
    }


    /**
     * Get the value of outletId
     */
    public function getOutletId()
    {
        return $this->outletId;
    }

    /**
     * Set the value of outletId
     *
     * @return  self
     */
    public function setOutletId($outletId)
    {
        $this->outletId = $outletId;

        return $this;
    }

    /**
     * Get the value of date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the value of date
     *
     * @return  self
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the value of items
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set the value of items
     *
     * @return  self
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }
}
