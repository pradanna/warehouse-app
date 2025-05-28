<?php

namespace App\Schemas\InventoryAdjustment;

use App\Commons\Schema\BaseSchema;

class InventoryAdjustmentSchema extends BaseSchema
{
    private $inventoryId;
    private $date;
    private $quantity;
    private $type;
    private $description;

    protected function rules()
    {
        return [
            'inventory_id' => 'required',
            'date' => 'required|date',
            'quantity' => 'required|numeric',
            'type' => 'required|in:in,out',
            'description' => 'string',
        ];
    }

    public function hydrateBody()
    {
        $inventoryId = $this->body['inventory_id'];
        $date = $this->body['date'];
        $quantity = $this->body['quantity'];
        $type = $this->body['type'];
        $description = !empty(trim($this->body['description'] ?? '')) ? $this->body['description'] : null;

        $this->setInventoryId($inventoryId)
            ->setQuantity($quantity)
            ->setDate($date)
            ->setType($type)
            ->setDescription($description);
    }

    /**
     * Get the value of inventoryId
     */
    public function getInventoryId()
    {
        return $this->inventoryId;
    }

    /**
     * Set the value of inventoryId
     *
     * @return  self
     */
    public function setInventoryId($inventoryId)
    {
        $this->inventoryId = $inventoryId;

        return $this;
    }

    /**
     * Get the value of date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the value of date
     *
     * @return  self
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the value of quantity
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set the value of quantity
     *
     * @return  self
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get the value of type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */
    public function setType($type)
    {
        $this->type = $type;

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
}
