<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomController;
use App\Schemas\Item\ItemQuery;
use App\Schemas\Item\ItemSchema;
use App\Services\Item\ItemService;
use Illuminate\Http\Request;

class ItemController extends CustomController
{
    /** @var ItemService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new ItemService();
    }

    public function create()
    {
        $body = $this->jsonBody();
        $schema = new ItemSchema();
        $schema->hydrateSchemaBody($body);
        return $this->service->create($schema);
    }

    public function findAll()
    {
        $queryParams = $this->queryParams();
        $query = new ItemQuery();
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
        $schema = new ItemSchema();
        $schema->hydrateSchemaBody($body);
        return $this->service->patch($id, $schema);
    }

    public function delete($id)
    {
        return $this->service->delete($id);
    }
}
