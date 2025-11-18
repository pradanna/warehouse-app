<?php

namespace App\Schemas\WarehouseExpense;

use App\Commons\Schema\BaseSchema;

class WarehouseExpenseQuery extends BaseSchema
{
    private $expenseCategoryId;
    private $page;
    private $perPage;
    private $dateStart;
    private $dateEnd;

    public function hydrateQuery()
    {
        $page = $this->query['page'] ?? 1;
        $perPage = $this->query['per_page'] ?? 10;
        $dateStart = !empty(trim($this->query['date_start'] ?? '')) ? $this->query['date_start'] : null;
        $dateEnd = !empty(trim($this->query['date_end'] ?? '')) ? $this->query['date_end'] : null;
        $expenseCategoryId = !empty(trim($this->query['expense_category_id'] ?? '')) ? $this->query['expense_category_id'] : null;
        $this->setPage($page)
            ->setPerPage($perPage)
            ->setDateStart($dateStart)
            ->setDateEnd($dateEnd)
            ->setExpenseCategoryId($expenseCategoryId);
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
}
