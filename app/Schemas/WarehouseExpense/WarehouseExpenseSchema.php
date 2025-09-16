<?php

namespace App\Schemas\WarehouseExpense;

use App\Commons\Schema\BaseSchema;

class WarehouseExpenseSchema extends BaseSchema
{
    private $expenseCategoryId;
    private $date;
    private $amount;
    private $description;

    protected function rules()
    {
        return [
            'expense_category_id' => 'required|uuid',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'description' => 'string'
        ];
    }

    public function hydrateBody()
    {
        $expenseCategoryId = $this->body['expense_category_id'];
        $date = $this->body['date'];
        $amount = $this->body['amount'];
        $description = !empty(trim($this->body['description'] ?? '')) ? $this->body['description'] : null;
        $this->setDate($date)
            ->setDescription($description)
            ->setExpenseCategoryId($expenseCategoryId)
            ->setAmount($amount);
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
