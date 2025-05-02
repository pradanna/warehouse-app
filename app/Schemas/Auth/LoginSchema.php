<?php

namespace App\Schemas\Auth;

use App\Commons\Schema\BaseSchema;
use Illuminate\Validation\Rules\Password;

class LoginSchema extends BaseSchema
{
    private $username;
    private $password;

    protected function rules()
    {
        return [
            'username' => 'required|string',
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->numbers()
                    ->mixedCase()
                    ->symbols(),
            ],
        ];
    }

    public function hydrateBody()
    {
        $username = $this->body['username'];
        $password = $this->body['password'];
        $this->setUsername($username)->setPassword($password);
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
}
