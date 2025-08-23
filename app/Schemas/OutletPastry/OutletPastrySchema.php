<?php

namespace App\Schemas\OutletPastry;

use App\Commons\Schema\BaseSchema;

class OutletPastrySchema extends BaseSchema
{
    private $outletId;
    private $date;
    private $referenceNumber;
    private $carts;

    protected function rules()
    {
        return [
            'outlet_id' => 'required|uuid',
            'date' => 'required|date',
            'reference_number' => 'string',
            'carts' => 'required|array|min:1',
            'carts.*.name' => 'required|string',
            'carts.*.qty' => 'required|numeric',
            'carts.*.price' => 'required|numeric',
        ];
    }

    public function hydrateBody()
    {
        $outletId = $this->body['outlet_id'];
        $date = $this->body['date'];
        $carts = $this->body['carts'];
        $referenceNumber = !empty(trim($this->body['reference_number'] ?? '')) ? $this->body['reference_number'] : null;
        $this->setOutletId($outletId)
            ->setDate($date)
            ->setReferenceNumber($referenceNumber)
            ->setCarts($carts);
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
     * Get the value of referenceNumber
     */
    public function getReferenceNumber()
    {
        return $this->referenceNumber;
    }

    /**
     * Set the value of referenceNumber
     *
     * @return  self
     */
    public function setReferenceNumber($referenceNumber)
    {
        $this->referenceNumber = $referenceNumber;

        return $this;
    }

    /**
     * Get the value of carts
     */
    public function getCarts()
    {
        return $this->carts;
    }

    /**
     * Set the value of carts
     *
     * @return  self
     */
    public function setCarts($carts)
    {
        $this->carts = $carts;

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
}
