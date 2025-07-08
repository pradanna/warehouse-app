<?php

namespace App\Schemas\Staff;

use App\Commons\Schema\BaseSchema;
use Illuminate\Validation\Rules\Password;

class StaffSchema extends BaseSchema
{
    private $username;
    private $password;
    private $name;
    private $phone;
    private $outletId;


    protected function rules()
    {
        return [
            'mode' => 'required|In:create,update',
            'username' => 'required|string',
            'password' => [
                'required_if:mode,create',
                'string',
                Password::min(8)
                    ->numbers()
                    ->mixedCase()
                    ->symbols(),
            ],
            'name' => 'required|string',
            'phone' => 'string',
            'outlet_id' => 'required|string',
        ];
    }

    public function hydrateBody()
    {
        $username = $this->body['username'];
        $password = !empty(trim($this->body['password'] ?? '')) ? $this->body['password'] : null;
        $name = $this->body['name'];
        $outletId = $this->body['outlet_id'];
        $phone = !empty(trim($this->body['phone'] ?? '')) ? $this->body['phone'] : null;
        $this
            ->setUsername($username)
            ->setPassword($password)
            ->setOutletId($outletId)
            ->setPhone($phone)
            ->setName($name);
    }

    /**
     * Get the value of username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @return  self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */
    public function setPassword($password)
    {
        $this->password = $password;

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
     * Get the value of outletId
     */
    public function getOutletId()
    {
        return $this->outletId;
    }

    /**
     * Set the value of outletId
     *
     * @return  self
     */
    public function setOutletId($outletId)
    {
        $this->outletId = $outletId;

        return $this;
    }

    /**
     * Get the value of phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set the value of phone
     *
     * @return  self
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }
}
