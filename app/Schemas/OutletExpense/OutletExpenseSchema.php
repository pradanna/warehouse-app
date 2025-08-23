<?php

namespace App\Schemas\OutletExpense;

use App\Commons\Schema\BaseSchema;

class OutletExpenseSchema extends BaseSchema
{
    private $outletId;
    private $expenseCategoryId;
    private $date;
    private $amount;
    private $description;

    protected function rules()
    {
        return [
            'outlet_id' => 'required|uuid',
            'expense_category_id' => 'required|uuid',
            'date' => 'required|date',
            'amount' => 'required|array',
            'amount.cash' => 'required|numeric',
            'amount.digital' => 'required|numeric',
            'description' => 'string'
        ];
    }

    public function hydrateBody()
    {
        $outletId = $this->body['outlet_id'];
        $expenseCategoryId = $this->body['expense_category_id'];
        $date = $this->body['date'];
        $amount = $this->body['amount'];
        $description = !empty(trim($this->body['description'] ?? '')) ? $this->body['description'] : null;
        $this->setOutletId($outletId)
            ->setDate($date)
            ->setDescription($description)
            ->setExpenseCategoryId($expenseCategoryId)
            ->setAmount($amount);
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
     * Get the value of expenseCategoryId
     */
    public function getExpenseCategoryId()
    {
        return $this->expenseCategoryId;
    }

    /**
     * Set the value of expenseCategoryId
     *
     * @return  self
     */
    public function setExpenseCategoryId($expenseCategoryId)
    {
        $this->expenseCategoryId = $expenseCategoryId;

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
}
