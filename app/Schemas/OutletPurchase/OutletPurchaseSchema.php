<?php

namespace App\Schemas\OutletPurchase;

use App\Commons\Schema\BaseSchema;

class OutletPurchaseSchema extends BaseSchema
{
    private $saleId;
    private $date;
    private $amount;
    private $cashFlow;

    protected function rules()
    {
        return [
            'sale_id' => 'required|uuid',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'cash_flow' => 'required|array',
            'cash_flow.date' => 'required|date',
            'cash_flow.name' => 'required|string',
        ];
    }

    public function hydrateBody()
    {
        $saleId = $this->body['sale_id'];
        $date = $this->body['date'];
        $amount = $this->body['amount'];
        $cashFlow = $this->body['cash_flow'];
        $this->setSaleId($saleId)
            ->setDate($date)
            ->setAmount($amount)
            ->setCashFlow($cashFlow);
    }


    /**
     * Get the value of saleId
     */
    public function getSaleId()
    {
        return $this->saleId;
    }

    /**
     * Set the value of saleId
     *
     * @return  self
     */
    public function setSaleId($saleId)
    {
        $this->saleId = $saleId;

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
     * Get the value of cashFlow
     */
    public function getCashFlow()
    {
        return $this->cashFlow;
    }

    /**
     * Set the value of cashFlow
     *
     * @return  self
     */
    public function setCashFlow($cashFlow)
    {
        $this->cashFlow = $cashFlow;

        return $this;
    }
}
