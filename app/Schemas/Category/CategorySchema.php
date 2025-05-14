<?php

namespace App\Schemas\Category;

use App\Commons\Schema\BaseSchema;

class CategorySchema extends BaseSchema
{
    private $name;
    private $description;

    protected function rules()
    {
        return [
            'name' => 'required|string',
            'description' => 'string'
        ];
    }

    public function hydrateBody()
    {
        $name = $this->body['name'];
        $description = $this->body['description'] ?? null;
        $this->setName($name)
            ->setDescription($description);
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
