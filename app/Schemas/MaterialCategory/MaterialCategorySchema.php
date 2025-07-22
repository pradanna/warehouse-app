<?php

namespace App\Schemas\MaterialCategory;

use App\Commons\Schema\BaseSchema;

class MaterialCategorySchema extends BaseSchema
{
    private $name;

    protected function rules()
    {
        return [
            'name' => 'required|string',
        ];
    }

    public function hydrateBody()
    {
        $name = $this->body['name'];
        $this->setName($name);
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
}
