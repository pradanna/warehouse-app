<?php

return [
    'issuer' => env('JWT_ISSUER', 'sync_care_app'),
    'secret' => env('JWT_SECRET', ''),
    'secret_refresh' => env('JWT_SECRET_REFRESH', ''),
    'exp' => env('JWT_EXPIRATION', 3600),
];
