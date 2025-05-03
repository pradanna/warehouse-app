<?php

namespace App\Services\Item;

use App\Commons\Http\ServiceResponse;
use App\Schemas\Item\ItemQuery;
use App\Schemas\Item\ItemSchema;

interface ItemServiceInterface
{
    public function create(ItemSchema $schema): ServiceResponse;
    public function findAll(ItemQuery $queryParams): ServiceResponse;
}
