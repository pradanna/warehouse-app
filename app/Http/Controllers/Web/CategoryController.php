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
        $response = $this->service->create($schema);
        return $this->toJSON($response);
    }

    public function findAll()
    {
        $queryParams = $this->queryParams();
        $query = new CategoryQuery();
        $query->hydrateSchemaQuery($queryParams);
        $response = $this->service->findAll($query);
        return $this->toJSON($response);
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return $this->toJSON($response);
    }

    public function patch($id)
    {
        $body = $this->jsonBody();
        $schema = new CategorySchema();
        $schema->hydrateSchemaBody($body);
        $response = $this->service->patch($id, $schema);
        return $this->toJSON($response);
    }

    public function delete($id)
    {
        $response = $this->service->delete($id);
        return $this->toJSON($response);
    }
}
