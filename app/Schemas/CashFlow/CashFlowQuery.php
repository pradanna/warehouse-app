<?php

namespace App\Schemas\CashFlow;

use App\Commons\Schema\BaseSchema;

class CashFlowQuery extends BaseSchema
{
    private $outletId;
    private $month;
    private $year;
    private $type;

    public function hydrateQuery()
    {
        $outletId = $this->query['outlet_id'];
        $month = $this->query['month'];
        $year = $this->query['year'];
        $type = !empty(trim($this->query['type'] ?? '')) ? $this->query['type'] : null;
        $this->setOutletId($outletId)
            ->setMonth($month)
            ->setYear($year)
            ->setType($type);
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
     * Get the value of month
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set the value of month
     *
     * @return  self
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get the value of year
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set the value of year
     *
     * @return  self
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get the value of type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
}
