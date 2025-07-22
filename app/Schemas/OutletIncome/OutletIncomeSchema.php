<?php

namespace App\Schemas\OutletIncome;

use App\Commons\Schema\BaseSchema;

class OutletIncomeSchema extends BaseSchema
{
    private $outletId;
    private $date;
    private $income;
    private $byMutation;
    private $description;

    protected function rules()
    {
        return [
            'outlet_id' => 'required|uuid',
            'date' => 'required|date',
            'income' => 'required|array',
            'income.cash' => 'required|numeric',
            'income.digital' => 'required|numeric',
            'income.by_mutation' => 'required|numeric',
            'description' => 'string'
        ];
    }

    public function hydrateBody()
    {
        $outletId = $this->body['outlet_id'];
        $date = $this->body['date'];
        $income = $this->body['income'];
        $description = !empty(trim($this->body['description'] ?? '')) ? $this->body['description'] : null;
        $this->setOutletId($outletId)
            ->setDate($date)
            ->setIncome($income)
            ->setDescription($description);
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
     * Get the value of income
     */
    public function getIncome()
    {
        return $this->income;
    }

    /**
     * Set the value of income
     *
     * @return  self
     */
    public function setIncome($income)
    {
        $this->income = $income;

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
     * Get the value of byMutation
     */
    public function getByMutation()
    {
        return $this->byMutation;
    }

    /**
     * Set the value of byMutation
     *
     * @return  self
     */
    public function setByMutation($byMutation)
    {
        $this->byMutation = $byMutation;

        return $this;
    }
}
