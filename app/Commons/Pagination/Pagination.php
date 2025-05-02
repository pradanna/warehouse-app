<?php

namespace App\Commons\Pagination;

use Illuminate\Database\Eloquent\Builder;

class Pagination
{
    private Builder $query;
    private $page;
    private $perPage;
    private $data;
    private $meta;
    private $jsonMeta;

    public function paginate()
    {
        $totalRows = $this->query->count();
        $totalPages = ceil($totalRows / $this->perPage);
        $offset = ($this->page - 1) * $this->perPage;
        $data = $this->query->limit($this->perPage)
            ->offset($offset)
            ->get();
        $this->setData($data);
        $this->setMeta(new MetaPagination($this->page, $this->perPage, $totalRows, $totalPages));
        $jsonMeta = MetaPagination::toJSON($this->page, $this->perPage, $totalRows, $totalPages);
        $this->setJsonMeta($jsonMeta);
    }

    /**
     * Get the value of query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set the value of query
     *
     * @return  self
     */
    public function setQuery($query)
    {
        $this->query = $query;

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
     * Get the value of meta
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Set the value of meta
     *
     * @return  self
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Get the value of jsonMeta
     */
    public function getJsonMeta()
    {
        return $this->jsonMeta;
    }

    /**
     * Set the value of jsonMeta
     *
     * @return  self
     */
    public function setJsonMeta($jsonMeta)
    {
        $this->jsonMeta = $jsonMeta;

        return $this;
    }

    /**
     * Get the value of data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of data
     *
     * @return  self
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
