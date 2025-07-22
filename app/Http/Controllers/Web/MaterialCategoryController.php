<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Http\Resources\MaterialCategory\MaterialCategoryCollection;
use App\Http\Resources\MaterialCategory\MaterialCategoryResource;
use App\Schemas\MaterialCategory\MaterialCategoryQuery;
use App\Schemas\MaterialCategory\MaterialCategorySchema;
use App\Services\MaterialCategory\MaterialCategoryService;
use Illuminate\Http\Request;

class MaterialCategoryController extends CustomController
{
    /** @var MaterialCategoryService $service */
    private $service;


    public function __construct()
    {
        parent::__construct();
        $this->service = new MaterialCategoryService();
    }

    public function create()
    {
        $schema = (new MaterialCategorySchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->create($schema);
        return (new MaterialCategoryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findAll()
    {
        $query = (new MaterialCategoryQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        return (new MaterialCategoryCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return (new MaterialCategoryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function patch($id)
    {
        $schema = (new MaterialCategorySchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->patch($id, $schema);
        return (new MaterialCategoryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function delete($id)
    {
        $response = $this->service->delete($id);
        return (new MaterialCategoryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
