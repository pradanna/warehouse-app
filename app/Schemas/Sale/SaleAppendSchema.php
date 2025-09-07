<?php

namespace App\Schemas\Sale;

use App\Commons\Schema\BaseSchema;

class SaleAppendSchema extends BaseSchema
{
    private $items;

    protected function rules()
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.inventory_id' => 'required|string',
            'items.*.quantity' => 'required|numeric',
            'items.*.price' => 'required|numeric',
        ];
    }

    public function hydrateBody()
    {
        $items = $this->body['items'];
        $dataItems = [];
        foreach ($items as $item) {
            $tmp['inventory_id'] = $item['inventory_id'];
            $tmp['quantity'] = $item['quantity'];
            $tmp['price'] = $item['price'];
            $tmp['total'] = $item['quantity'] * $item['price'];
            array_push($dataItems, $tmp);
        }
        $this->setItems($dataItems);
    }


    /**
     * Get the value of items
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set the value of items
     *
     * @return  self
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }
}
