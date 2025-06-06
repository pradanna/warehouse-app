<?php

namespace App\Schemas\Purchase;

use App\Commons\Schema\BaseSchema;

class PurchaseSchema extends BaseSchema
{
    private $supplierId;
    private $date;
    private $referenceNumber;
    private $discount;
    private $tax;
    private $description;
    private $paymentType;
    private $paymentStatus;
    private $items;
    private $payment;

    protected function rules()
    {
        return [
            'supplier_id' => 'string',
            'date' => 'required|date',
            'reference_number' => 'string',
            'discount' => 'required|numeric',
            'tax' => 'required|numeric',
            'description' => 'string',
            'payment_type' => 'required|in:cash,installment',
            'items' => 'required|array|min:1',
            'items.*.inventory_id' => 'required|string',
            'items.*.quantity' => 'required|numeric',
            'items.*.price' => 'required|numeric',
            'payment' => 'required_if:payment_type,cash|array',
            'payment.date' => 'required_if:payment_type,cash|date',
            'payment.description' => 'required_if:payment_type,cash|string',
            'payment.payment_type' => 'required_if:payment_type,cash|in:cash,digital',
            'payment.amount' => 'required_if:payment_type,cash|numeric',
        ];
    }

    public function hydrateBody()
    {
        $supplierId = $this->body['supplier_id'];
        $date = $this->body['date'];
        $referenceNumber =  !empty(trim($this->body['reference_number'] ?? '')) ? $this->body['reference_number'] : null;
        $discount = $this->body['discount'];
        $tax = $this->body['tax'];
        $description = !empty(trim($this->body['description'] ?? '')) ? $this->body['description'] : null;
        $paymentType = $this->body['payment_type'];
        $items = $this->body['items'];
        $payment = null;
        if (isset($this->body['payment'])) {
            $paymentDate = $this->body['payment']['date'];
            $paymentDescription = $this->body['payment']['description'] ?? null;
            $paymentPaymentType = $this->body['payment']['payment_type'];
            $paymentAmount = $this->body['payment']['amount'];
            $payment = [
                'date' => $paymentDate,
                'description' => $paymentDescription,
                'payment_type' => $paymentPaymentType,
                'amount' => $paymentAmount
            ];
        }


        $dataItems = [];
        foreach ($items as $item) {
            $tmp['inventory_id'] = $item['inventory_id'];
            $tmp['quantity'] = $item['quantity'];
            $tmp['price'] = $item['price'];
            $tmp['total'] = $item['quantity'] * $item['price'];
            array_push($dataItems, $tmp);
        }

        $this->setSupplierId($supplierId)
            ->setDate($date)
            ->setReferenceNumber($referenceNumber)
            ->setDiscount($discount)
            ->setTax($tax)
            ->setDescription($description)
            ->setPaymentType($paymentType)
            ->setItems($dataItems)
            ->setPayment($payment);
    }

    /**
     * Get the value of supplierId
     */
    public function getSupplierId()
    {
        return $this->supplierId;
    }

    /**
     * Set the value of supplierId
     *
     * @return  self
     */
    public function setSupplierId($supplierId)
    {
        $this->supplierId = $supplierId;

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
     * Get the value of discount
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set the value of discount
     *
     * @return  self
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get the value of tax
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Set the value of tax
     *
     * @return  self
     */
    public function setTax($tax)
    {
        $this->tax = $tax;

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
     * Get the value of paymentStatus
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * Set the value of paymentStatus
     *
     * @return  self
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;

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

    /**
     * Get the value of payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Set the value of payment
     *
     * @return  self
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;

        return $this;
    }
}
