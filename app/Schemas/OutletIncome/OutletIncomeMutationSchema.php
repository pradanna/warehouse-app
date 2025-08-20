<?php

namespace App\Schemas\OutletIncome;

use App\Commons\Schema\BaseSchema;

class OutletIncomeMutationSchema extends BaseSchema
{
    private $amount;
    private $date;

    protected function rules()
    {
        return [
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ];
    }

    public function hydrateBody()
    {
        $amount = $this->body['amount'];
        $date = $this->body['date'];
        $this->setAmount($amount)
            ->setDate($date);
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
}
