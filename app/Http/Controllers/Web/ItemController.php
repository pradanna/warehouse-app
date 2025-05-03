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
        $response = $this->service->create($schema);
        return $this->toJSON($response);
    }

    public function findAll()
    {
        $queryParams = $this->queryParams();
        $query = new ItemQuery();
        $query->hydrateSchemaQuery($queryParams);
        $response = $this->service->findAll($query);
        return $this->toJSON($response);
    }
}
