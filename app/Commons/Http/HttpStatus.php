<?php

namespace App\Commons\Http;

enum HttpStatus: int
{
    case OK = 200;
    case Created = 201;
    case BadRequest = 400;
    case Unauthorized = 401;
    case Forbidden = 403;
    case NotFound = 404;
    case UnprocessableEntity = 422;
    case InternalServerError = 500;
}
