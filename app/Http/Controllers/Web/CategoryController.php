<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\CustomController;
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
}
