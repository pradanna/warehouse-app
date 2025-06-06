<?php

namespace App\Commons\Schema;

use Illuminate\Support\Facades\Validator;

class BaseSchema
{
    protected $body;
    protected $query;

    public function hydrateSchemaBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function hydrateSchemaQuery($query)
    {
        $this->query = $query;
        return $this;
    }

    protected function rules()
    {
        return [];
    }

    protected function messages()
    {
        return [];
    }

    public function validate()
    {
        return Validator::make($this->body, $this->rules(), $this->messages());
    }

    public function hydrateBody()
    {

    }

    public function hydrateQuery()
    {

    }
}
