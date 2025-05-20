<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\CustomController;
use App\Schemas\Category\CategoryQuery;
use App\Schemas\Category\CategorySchema;
use App\Services\Category\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends CustomController
{
    /** @var CategoryService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new CategoryService();
    }

    public function create()
    {
        $body = $this->jsonBody();
        $schema = new CategorySchema();
        $schema->hydrateSchemaBody($body);
        return $this->service->create($schema);
    }

    public function findAll()
    {
        $queryParams = $this->queryParams();
        $query = new CategoryQuery();
        $query->hydrateSchemaQuery($queryParams);
        return $this->service->findAll($query);
    }

    public function findByID($id)
    {
        return $this->service->findByID($id);
    }

    public function patch($id)
    {
        $body = $this->jsonBody();
        $schema = new CategorySchema();
        $schema->hydrateSchemaBody($body);
        return $this->service->patch($id, $schema);
    }

    public function delete($id)
    {
        return $this->service->delete($id);
    }
}
