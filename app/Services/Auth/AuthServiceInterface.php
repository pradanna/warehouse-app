<?php

namespace App\Services\Auth;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Auth\LoginSchema;
use App\Schemas\Auth\RefreshTokenSchema;

interface AuthServiceInterface
{
    public function login(LoginSchema $schema): ServiceResponse;
    public function refreshToken(RefreshTokenSchema $schema): ServiceResponse;
}
