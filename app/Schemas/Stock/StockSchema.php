<?php

namespace App\Schemas\Stock;

use App\Commons\Schema\BaseSchema;

class StockSchema extends BaseSchema
{
    private $itemId;
    private $unitId;
    private $sku;
    private $description;
    private $price;
    private $currentStock;
    private $minStock;
    private $maxStock;

    protected function rules()
    {
        return [
            'item_id' => 'required',
            'unit_id' => 'required',
            'sku' => 'required|string',
            'description' => 'string',
            'price' => 'required|numeric',
            'min_stock' => 'required|numeric',
            'max_stock' => 'required|numeric'
        ];
    }

    public function hydrateBody()
    {
        $itemId = $this->body['item_id'];
        $unitId = $this->body['unit_id'];
        $sku = $this->body['sku'] ?? null;
        $description = $this->body['description'] ?? null;
        $price = $this->body['price'];
        $currentStock = $this->body['current_stock'];
        $minStock = $this->body['min_stock'];
        $maxStock = $this->body['max_stock'];

        $this->setItemId($itemId)
            ->setUnitId($unitId)
            ->setSku($sku)
            ->setDescription($description)
            ->setPrice($price)
            ->setCurrentStock($currentStock)
            ->setMinStock($minStock)
            ->setMaxStock($maxStock);
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
     * Get the value of price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the value of price
     *
     * @return  self
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get the value of currentStock
     */
    public function getCurrentStock()
    {
        return $this->currentStock;
    }

    /**
     * Set the value of currentStock
     *
     * @return  self
     */
    public function setCurrentStock($currentStock)
    {
        $this->currentStock = $currentStock;

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
}
