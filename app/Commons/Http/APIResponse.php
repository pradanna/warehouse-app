<?php

namespace App\Commons\Http;

use Illuminate\Support\Facades\Response;

class APIResponse
{
    public static function toJSONResponse(
        $status = 500,
        $message = '',
        $data = null,
        $meta = null
    )
    {
        return Response::json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'meta' => $meta
        ], $status);
    }
}
