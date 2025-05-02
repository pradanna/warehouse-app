<?php

namespace App\Services\Auth;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Auth\LoginSchema;

interface AuthServiceInterface
{
    public function login(LoginSchema $schema): ServiceResponse;
}
