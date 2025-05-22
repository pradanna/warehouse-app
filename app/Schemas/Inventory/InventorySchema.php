<?php

namespace App\Schemas\Inventory;

use App\Commons\Schema\BaseSchema;

class InventorySchema extends BaseSchema
{
    private $itemId;
    private $unitId;
    private $sku;
    private $description;
    private $minStock;
    private $maxStock;
    private array $prices;

    protected function rules()
    {
        return [
            'item_id' => 'required',
            'unit_id' => 'required',
            'sku' => 'string',
            'description' => 'string',
            'min_stock' => 'numeric',
            'max_stock' => 'numeric',
            'prices' => 'required|array|min:1',
            'prices.*.outlet_id' => 'required|string',
            'prices.*.price' => 'required|numeric'
        ];
    }

    public function hydrateBody()
    {
        $itemId = $this->body['item_id'];
        $unitId = $this->body['unit_id'];
        $sku = !empty(trim($this->body['sku'] ?? '')) ? $this->body['sku'] : null;
        $description = !empty(trim($this->body['description'] ?? '')) ? $this->body['description'] : null;;
        $minStock = $this->body['min_stock'] ?? 0;
        $maxStock = $this->body['max_stock'] ?? 99;
        $prices = $this->body['prices'];

        $dataPrices = [];
        foreach ($prices as $price) {
            $tmp['outlet_id'] = $price['outlet_id'];
            $tmp['price'] = $price['price'];
            array_push($dataPrices, $tmp);
        }

        $this->setItemId($itemId)
            ->setUnitId($unitId)
            ->setSku($sku)
            ->setDescription($description)
            ->setMinStock($minStock)
            ->setMaxStock($maxStock)
            ->setPrices($dataPrices);
    }

    /**
     * Get the value of itemId
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * Set the value of itemId
     *
     * @return  self
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

        return $this;
    }

    /**
     * Get the value of unitId
     */
    public function getUnitId()
    {
        return $this->unitId;
    }

    /**
     * Set the value of unitId
     *
     * @return  self
     */
    public function setUnitId($unitId)
    {
        $this->unitId = $unitId;

        return $this;
    }

    /**
     * Get the value of sku
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set the value of sku
     *
     * @return  self
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get the value of description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of minStock
     */
    public function getMinStock()
    {
        return $this->minStock;
    }

    /**
     * Set the value of minStock
     *
     * @return  self
     */
    public function setMinStock($minStock)
    {
        $this->minStock = $minStock;

        return $this;
    }

    /**
     * Get the value of maxStock
     */
    public function getMaxStock()
    {
        return $this->maxStock;
    }

    /**
     * Set the value of maxStock
     *
     * @return  self
     */
    public function setMaxStock($maxStock)
    {
        $this->maxStock = $maxStock;

        return $this;
    }

    /**
     * Get the value of prices
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * Set the value of prices
     *
     * @return  self
     */
    public function setPrices($prices)
    {
        $this->prices = $prices;

        return $this;
    }
}
