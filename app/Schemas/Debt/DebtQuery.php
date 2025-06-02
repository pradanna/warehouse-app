<?php

namespace App\Schemas\Debt;

use App\Commons\Schema\BaseSchema;

class DebtQuery extends BaseSchema
{
    private $page;
    private $perPage;
    private $supplierId;
    private $status;

    public function hydrateQuery()
    {
        $page = $this->query['page'] ?? 1;
        $perPage = $this->query['per_page'] ?? 10;
        $supplierId = !empty(trim($this->query['supplier_id'] ?? '')) ? $this->query['supplier_id'] : null;
        $status = !empty(trim($this->query['status'] ?? '')) ? $this->query['status'] : null;
        $this->setPage($page)
            ->setPerPage($perPage)
            ->setSupplierId($supplierId)
            ->setStatus($status);
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
     * Get the value of status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
}
