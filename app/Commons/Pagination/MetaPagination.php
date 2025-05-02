<?php

namespace App\Commons\Pagination;

class MetaPagination
{
    private $page;
    private $perPage;
    private $totalRows;
    private $totalPages;


    /**
     * MetaPagination constructor.
     *
     * @param int $page The current page number.
     * @param int $perPage The number of items per page.
     * @param int $totalRows The total number of rows/items.
     * @param int $totalPages The total number of pages.
     */
    public function __construct($page, $perPage, $totalRows, $totalPages)
    {
        $this->page = $page;
        $this->perPage = $perPage;
        $this->totalRows = $totalRows;
        $this->totalPages = $totalPages;
    }


    public static function toJSON($page, $perPage, $totalRows, $totalPages)
    {
        return [
            'page' => (int) $page,
            'per_page' => (int) $perPage,
            'total_rows' => $totalRows,
            'total_pages' => $totalPages
        ];
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
     * Get the value of totalRows
     */
    public function getTotalRows()
    {
        return $this->totalRows;
    }

    /**
     * Set the value of totalRows
     *
     * @return  self
     */
    public function setTotalRows($totalRows)
    {
        $this->totalRows = $totalRows;

        return $this;
    }

    /**
     * Get the value of totalPages
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }

    /**
     * Set the value of totalPages
     *
     * @return  self
     */
    public function setTotalPages($totalPages)
    {
        $this->totalPages = $totalPages;

        return $this;
    }
}
