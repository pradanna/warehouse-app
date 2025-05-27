<?php

namespace App\Schemas\PurchasePayment;

use App\Commons\Schema\BaseSchema;

class PurchasePaymentSchema extends BaseSchema
{
    private $purchaseId;
    private $date;
    private $paymentType;
    private $amount;
    private $description;

    protected function rules()
    {
        return [
            'purchase_id' => 'required',
            'date' => 'required|date',
            'payment_type' => 'required|in:cash,digital',
            'amount' => 'required|numeric',
            'description' => 'string'
        ];
    }

    public function hydrateBody()
    {
        $purchaseId = $this->body['purchase_id'];
        $date = $this->body['date'];
        $paymentType = $this->body['payment_type'];
        $amount = $this->body['amount'];
        $description = !empty(trim($this->body['description'] ?? '')) ? $this->body['description'] : null;

        $this
            ->setPurchaseId($purchaseId)
            ->setDate($date)
            ->setPaymentType($paymentType)
            ->setAmount($amount)
            ->setDescription($description);
    }

    /**
     * Get the value of purchaseId
     */
    public function getPurchaseId()
    {
        return $this->purchaseId;
    }

    /**
     * Set the value of purchaseId
     *
     * @return  self
     */
    public function setPurchaseId($purchaseId)
    {
        $this->purchaseId = $purchaseId;

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
