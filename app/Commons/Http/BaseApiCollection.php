<?php

namespace App\Commons\Http;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseApiCollection extends ResourceCollection implements Responsable
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
        if ($this->resource instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $this->meta = [
                'page' => $this->currentPage(),
                'per_page' => $this->perPage(),
                'total_rows' => $this->total(),
                'total_pages' => $this->lastPage(),
            ];
        }

        return response()->json([
            'status' => $this->httpStatus->value,
            'message' => $this->message,
            'data' => $this->collection,
            'meta' => $this->meta
        ], $this->httpStatus->value);
    }
}
