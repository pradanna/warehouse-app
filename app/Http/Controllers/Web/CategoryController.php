<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\CustomController;
use App\Http\Resources\Category\CategoryCollection;
use App\Http\Resources\Category\CategoryResource;
use App\Schemas\Category\CategoryQuery;
use App\Schemas\Category\CategorySchema;
use App\Services\Category\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends CustomController
{
    /** @var CategoryService $service */
    private $service;

    private $categoryResource;
    private $categoryCollection;

    public function __construct()
    {
        parent::__construct();
        $this->service = new CategoryService();
        $this->categoryResource = null;
        $this->categoryCollection = [];
    }

    public function create()
    {
        $schema = (new CategorySchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->create($schema);
        return (new CategoryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findAll()
    {
        $query = (new CategoryQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        $this->categoryCollection = $response->getData();
        return (new CategoryCollection($this->categoryCollection))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return (new CategoryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
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
