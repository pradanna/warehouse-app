<?php

namespace App\Commons\Http;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BaseApiResource extends JsonResource implements Responsable
{
    protected HttpStatus $httpStatus = HttpStatus::InternalServerError;
    protected string $message = 'internal server error';
    protected mixed $meta = null;

    public function withStatus(HttpStatus $httpStatus): static
    {
        $this->httpStatus = $httpStatus;
        return $this;
    }

    public function withMessage(?string $message = null): static
    {
        $this->message = $message;
        return $this;
    }

    public function withMeta($meta): static
    {
        $this->meta = $meta;
        return $this;
    }

    public function toResponse($request): JsonResponse
    {
        $response = [
            'status' => $this->httpStatus->value,
            'message' => $this->message,
        ];

        $data = is_null($this->resource) ? null : $this->toArray($request);
        if (!is_null($data)) {
            $response['data'] = $data;
        }

        if (!is_null($this->meta)) {
            $response['meta'] = $this->meta;
        }

        if (!empty($this->additional)) {
            $response = array_merge($response, $this->additional);
        }
        return response()->json($response, $this->httpStatus->value);
    }
}
