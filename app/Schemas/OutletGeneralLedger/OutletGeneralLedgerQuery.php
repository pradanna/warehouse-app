<?php

namespace App\Schemas\OutletGeneralLedger;

use App\Commons\Schema\BaseSchema;

class OutletGeneralLedgerQuery extends BaseSchema
{
    private $outletId;
    private $month;
    private $year;

    public function hydrateQuery()
    {
        $outletId = $this->query['outlet_id'];
        $month = $this->query['month'];
        $year = $this->query['year'];
        $this->setOutletId($outletId)
            ->setMonth($month)
            ->setYear($year);
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
}
