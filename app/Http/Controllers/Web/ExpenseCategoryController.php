<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Http\Resources\ExpenseCategory\ExpenseCategoryCollection;
use App\Http\Resources\ExpenseCategory\ExpenseCategoryResource;
use App\Schemas\ExpenseCategory\ExpenseCategoryQuery;
use App\Schemas\ExpenseCategory\ExpenseCategorySchema;
use App\Services\ExpenseCategory\ExpenseCategoryService;
use Illuminate\Http\Request;

class ExpenseCategoryController extends CustomController
{
    /** @var ExpenseCategoryService $service */
    private $service;


    public function __construct()
    {
        parent::__construct();
        $this->service = new ExpenseCategoryService();
    }

    public function create()
    {
        $schema = (new ExpenseCategorySchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->create($schema);
        return (new ExpenseCategoryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findAll()
    {
        $query = (new ExpenseCategoryQuery())->hydrateSchemaQuery($this->queryParams());
        $response = $this->service->findAll($query);
        return (new ExpenseCategoryCollection($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function findByID($id)
    {
        $response = $this->service->findByID($id);
        return (new ExpenseCategoryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function patch($id)
    {
        $schema = (new ExpenseCategorySchema())->hydrateSchemaBody($this->jsonBody());
        $response = $this->service->patch($id, $schema);
        return (new ExpenseCategoryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }

    public function delete($id)
    {
        $response = $this->service->delete($id);
        return (new ExpenseCategoryResource($response->getData()))
            ->withStatus($response->getStatus())
            ->withMessage($response->getMessage());
    }
}
