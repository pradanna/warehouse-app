<?php

namespace App\Schemas\Auth;

use App\Commons\Schema\BaseSchema;

class RefreshTokenSchema extends BaseSchema
{
    private $refreshToken;

    protected function rules()
    {
        return [
            'refresh_token' => 'required',
        ];
    }

    public function hydrateBody()
    {
        $refreshToken = $this->body['refresh_token'];
        $this->setRefreshToken($refreshToken);
    }


    /**
     * Get the value of refreshToken
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * Set the value of refreshToken
     *
     * @return  self
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }
}
