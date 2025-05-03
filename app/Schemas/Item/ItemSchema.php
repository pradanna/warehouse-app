<?php

namespace App\Schemas\Item;

use App\Commons\Schema\BaseSchema;

class ItemSchema extends BaseSchema
{
    private $categoryId;
    private $sku;
    private $name;
    private $unit;
    private $description;
    private $price;
    private $currentStock;
    private $minStock;
    private $maxStock;

    protected function rules()
    {
        return [
            'category_id' => 'required',
            'sku' => 'string',
            'name' => 'required|string',
            'unit' => 'required|string',
            'description' => 'string',
            'price' => 'required|numeric',
            'current_stock' => 'required|numeric',
            'min_stock' => 'required|numeric',
            'max_stock' => 'required|numeric'
        ];
    }

    public function hydrateBody()
    {
        $categoryId = $this->body['category_id'];
        $sku = $this->body['sku'] ?? null;
        $name = $this->body['name'];
        $unit = $this->body['unit'];
        $description = $this->body['description'] ?? null;
        $price = $this->body['price'];
        $currentStock = $this->body['current_stock'];
        $minStock = $this->body['min_stock'];
        $maxStock = $this->body['max_stock'];

        $this->setCategoryId($categoryId)
            ->setSku($sku)
            ->setName($name)
            ->setUnit($unit)
            ->setDescription($description)
            ->setPrice($price)
            ->setCurrentStock($currentStock)
            ->setMinStock($minStock)
            ->setMaxStock($maxStock);
    }


    /**
     * Get the value of categoryId
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set the value of categoryId
     *
     * @return  self
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

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
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of unit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set the value of unit
     *
     * @return  self
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

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
