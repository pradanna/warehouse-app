<?php

namespace App\Schemas\InventoryAdjustment;

use App\Commons\Schema\BaseSchema;

class InventoryAdjustmentQuery extends BaseSchema
{
    private $page;
    private $perPage;
    private $param;
    private $type;
    private $dateStart;
    private $dateEnd;

    public function hydrateQuery()
    {
        $page = $this->query['page'] ?? 1;
        $perPage = $this->query['per_page'] ?? 10;
        $param = $this->query['param'] ?? '';
        $dateStart = !empty(trim($this->query['date_start'] ?? '')) ? $this->query['date_start'] : null;
        $dateEnd = !empty(trim($this->query['date_end'] ?? '')) ? $this->query['date_end'] : null;
        $type = !empty(trim($this->query['type'] ?? '')) ? $this->query['type'] : null;
        $this->setPage($page)
            ->setPerPage($perPage)
            ->setParam($param)
            ->setDateStart($dateStart)
            ->setDateEnd($dateEnd)
            ->setType($type);
    }

    /**
     * Get the value of perPage
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * Set the value of perPage
     *
     * @return  self
     */
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Get the value of page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set the value of page
     *
     * @return  self
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get the value of param
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * Set the value of param
     *
     * @return  self
     */
    public function setParam($param)
    {
        $this->param = $param;

        return $this;
    }

    /**
     * Get the value of dateStart
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Set the value of dateStart
     *
     * @return  self
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * Get the value of dateEnd
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * Set the value of dateEnd
     *
     * @return  self
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;

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
