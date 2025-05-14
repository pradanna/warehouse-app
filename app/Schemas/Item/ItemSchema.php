<?php

namespace App\Schemas\Item;

use App\Commons\Schema\BaseSchema;

class ItemSchema extends BaseSchema
{
    private $categoryId;
    private $name;
    private $description;

    protected function rules()
    {
        return [
            'category_id' => 'required',
            'name' => 'required|string',
            'description' => 'string',
        ];
    }

    public function hydrateBody()
    {
        $categoryId = $this->body['category_id'];
        $name = $this->body['name'];
        $description = $this->body['description'] ?? null;

        $this->setCategoryId($categoryId)
            ->setName($name)
            ->setDescription($description);
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
