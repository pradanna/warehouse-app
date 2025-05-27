<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Commons\Http\APIResponse;
use App\Commons\Http\ServiceResponse;

class CustomController extends Controller
{
    /** @var Request $request */
    protected $request;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
    }

    public function formBody()
    {
        return $this->request->all();
    }

    public function jsonBody()
    {
        return $this->request->json()->all();
    }

    public function queryParams()
    {
        return $this->request->query();
    }

    public function toJSON(ServiceResponse $serviceResponse)
    {
        return APIResponse::toJSONResponse(
            $serviceResponse->getStatus()->value,
            $serviceResponse->getMessage(),
            $serviceResponse->getData(),
            $serviceResponse->getMeta()
        );
    }
}
