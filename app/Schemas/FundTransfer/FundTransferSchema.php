<?php

namespace App\Schemas\FundTransfer;

use App\Commons\Schema\BaseSchema;

class FundTransferSchema extends BaseSchema
{
    private $outletId;
    private $amount;
    private $date;
    private $transferTo;

    protected function rules()
    {
        return [
            'outlet_id' => 'required|string',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'transfer_to' => 'required|in:digital,cash',
        ];
    }

    public function hydrateBody()
    {
        $outletId = $this->body['outlet_id'];
        $amount = $this->body['amount'];
        $date = $this->body['date'];
        $transferTo = $this->body['transfer_to'];
        $this->setOutletId($outletId)
            ->setAmount($amount)
            ->setDate($date)
            ->setTransferTo($transferTo);
    }

    /**
     * Get the value of amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set the value of amount
     *
     * @return  self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

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
     * Get the value of transferTo
     */
    public function getTransferTo()
    {
        return $this->transferTo;
    }

    /**
     * Set the value of transferTo
     *
     * @return  self
     */
    public function setTransferTo($transferTo)
    {
        $this->transferTo = $transferTo;

        return $this;
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
}
