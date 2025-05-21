<?php

namespace App\Services\Item;

use App\Schemas\Item\ItemQuery;
use App\Schemas\Item\ItemSchema;
use Illuminate\Contracts\Support\Responsable;

interface ItemServiceInterface
{
    public function create(ItemSchema $schema): Responsable;
    public function findAll(ItemQuery $queryParams): Responsable;
    public function findByID($id): Responsable;
    public function patch($id, ItemSchema $schema): Responsable;
    public function delete($id): Responsable;
}
