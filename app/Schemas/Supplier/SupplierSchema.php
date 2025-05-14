<?php

namespace App\Schemas\Supplier;

use App\Commons\Schema\BaseSchema;

class SupplierSchema extends BaseSchema
{
    private $name;
    private $address;
    private $contact;

    protected function rules()
    {
        return [
            'name' => 'required|string',
            'address' => 'required|string',
            'contact' => 'required|string'
        ];
    }

    public function hydrateBody()
    {
        $name = $this->body['name'];
        $address = $this->body['address'];
        $contact = $this->body['contact'];

        $this->setName($name)
            ->setAddress($address)
            ->setContact($contact);
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
     * Get the value of address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the value of address
     *
     * @return  self
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get the value of contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set the value of contact
     *
     * @return  self
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }
}
