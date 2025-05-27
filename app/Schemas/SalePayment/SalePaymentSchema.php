<?php

namespace App\Schemas\SalePayment;

use App\Commons\Schema\BaseSchema;

class SalePaymentSchema extends BaseSchema
{
    private $saleId;
    private $date;
    private $paymentType;
    private $amount;
    private $description;

    protected function rules()
    {
        return [
            'sale_id' => 'required',
            'date' => 'required|date',
            'payment_type' => 'required|in:cash,digital',
            'amount' => 'required|numeric',
            'description' => 'string'
        ];
    }

    public function hydrateBody()
    {
        $saleId = $this->body['sale_id'];
        $date = $this->body['date'];
        $paymentType = $this->body['payment_type'];
        $amount = $this->body['amount'];
        $description = !empty(trim($this->body['description'] ?? '')) ? $this->body['description'] : null;

        $this
            ->setSaleId($saleId)
            ->setDate($date)
            ->setPaymentType($paymentType)
            ->setAmount($amount)
            ->setDescription($description);
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
     * Get the value of paymentType
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * Set the value of paymentType
     *
     * @return  self
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;

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
     * Get the value of description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
