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


    public function __construct()
    {
        parent::__construct();
        $this->service = new CategoryService();
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
        return (new CategoryCollection($response->getData()))
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
        $schema = (new CategorySchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->patch($id, $schema);
        return (new CategoryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function delete($id)
    {
        $response = $this->service->delete($id);
        return (new CategoryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
