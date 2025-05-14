<?php

namespace App\Commons\Http;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Http\JsonResponse;

class BaseResource extends JsonResource
{

    protected string $message = 'Successfully retrieved data';
    protected array|null $meta = null;
    protected HttpStatus $httpStatus = HttpStatus::OK;

    public function withStatus(HttpStatus $httpStatus): static
    {
        $this->httpStatus = $httpStatus;
        return $this;
    }

    public function withMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function withMeta(array $meta): static
    {
        $this->meta = $meta;
        return $this;
    }

    // Method wajib di-override oleh turunan
    protected function toItemArray($item, Request $request): array
    {
        return [];
    }

    public function toArray($request): mixed
    {
        if (is_null($this->resource)) return null;

        if ($this->resource instanceof Collection || $this->resource instanceof AbstractPaginator) {
            return $this->resource->map(function ($item) use ($request) {
                return $this->toItemArray($item, $request);
            })->values();
        }

        return $this->toItemArray($this->resource, $request);
    }

    public function toResponse($request): JsonResponse
    {
        return response()->json([
            'status'  => $this->httpStatus->value,
            'message' => $this->message,
            'data'    => $this->toArray($request),
            'meta'    => $this->meta,
        ], $this->httpStatus->value);
    }
}
