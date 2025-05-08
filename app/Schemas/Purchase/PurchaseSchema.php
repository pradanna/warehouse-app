<?php

namespace App\Schemas\Purchase;

use App\Commons\Schema\BaseSchema;

class PurchaseSchema extends BaseSchema
{
    private $supplierId;
    private $date;
    private $referenceNumber;
    private $subTotal;
    private $discount;
    private $tax;
    private $total;
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
            'sub_total' => 'required|numeric',
            'discount' => 'required|numeric',
            'tax' => 'required|numeric',
            'total' => 'required|numeric',
            'description' => 'string',
            'payment_type' => 'required|in:cash,installment',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|string',
            'items.*.unit_id' => 'required|string',
            'items.*.quantity' => 'required|numeric',
            'items.*.price' => 'required|numeric',
            'items.*.total' => 'required|numeric',
            'payment' => 'required|array',
            'payment.date' => 'required|date',
            'payment.description' => 'string',
            'payment.payment_type' => 'required|in:cash,digital',
            'payment.amount' => 'required|numeric',
        ];
    }

    public function hydrateBody()
    {
        $supplierId = $this->body['supplier_id'];
        $date = $this->body['date'];
        $referenceNumber = $this->body['reference_number'] ?? null;
        $subTotal = $this->body['sub_total'];
        $discount = $this->body['discount'];
        $tax = $this->body['tax'];
        $total = $this->body['total'];
        $description = $this->body['description'] ?? null;
        $paymentType = $this->body['payment_type'];
        $items = $this->body['items'];
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

        $dataItems = [];
        foreach ($items as $item) {
            $tmp['item_id'] = $item['item_id'];
            $tmp['unit_id'] = $item['unit_id'];
            $tmp['quantity'] = $item['quantity'];
            $tmp['price'] = $item['price'];
            $tmp['total'] = $item['total'];
            array_push($dataItems, $tmp);
        }

        $this->setSupplierId($supplierId)
            ->setDate($date)
            ->setReferenceNumber($referenceNumber)
            ->setSubTotal($subTotal)
            ->setDiscount($discount)
            ->setTax($tax)
            ->setTotal($total)
            ->setDescription($description)
            // ->setPaymentStatus($paymentStatus)
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
     * Get the value of subTotal
     */
    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * Set the value of subTotal
     *
     * @return  self
     */
    public function setSubTotal($subTotal)
    {
        $this->subTotal = $subTotal;

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
     * Get the value of total
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set the value of total
     *
     * @return  self
     */
    public function setTotal($total)
    {
        $this->total = $total;

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
