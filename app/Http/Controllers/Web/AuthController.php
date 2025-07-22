<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\CustomController;
use App\Schemas\Auth\LoginSchema;
use App\Schemas\Auth\RefreshTokenSchema;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;

class AuthController extends CustomController
{
    /** @var AuthService $service */
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new AuthService();
    }

    public function login()
    {
        $body = $this->jsonBody();
        $schema = new LoginSchema();
        $schema->hydrateSchemaBody($body);
        $response = $this->service->login($schema);
        return $this->toJSON($response);
    }

    public function refreshToken()
    {
        $body = $this->jsonBody();
        $schema = new RefreshTokenSchema();
        $schema->hydrateSchemaBody($body);
        $response = $this->service->refreshToken($schema);
        return $this->toJSON($response);
    }
}
