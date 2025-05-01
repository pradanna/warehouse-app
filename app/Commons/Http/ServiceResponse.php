<?php

namespace App\Commons\Http;

class ServiceResponse
{
    private $success;
    private $status;
    private $message;
    private $data;
    private $meta;

    /**
     * ServiceResponse constructor.
     * @param bool $success
     * @param int $status
     * @param string $message
     * @param mixed | null $data
     * @param mixed | null $meta
     */
    public function __construct(
        $success = false,
        $status = 500,
        $message = '',
        $data = null,
        $meta = null
    )
    {
        $this->success = $success;
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
        $this->meta = $meta;
    }

    /**
     * @return mixed
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * @param mixed $success
     * @return ServiceResponse
     */
    public function setSuccess($success)
    {
        $this->success = $success;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return ServiceResponse
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     * @return ServiceResponse
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return ServiceResponse
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param mixed $meta
     * @return ServiceResponse
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;
        return $this;
    }

    public static function statusOK($message = 'success', $data = null, $meta = null): self
    {
        return new self(true, 200, $message, $data, $meta);
    }

    public static function statusCreated($message = 'success', $data = null, $meta = null): self
    {
        return new self(true, 201, $message, $data, $meta);
    }

    public static function internalServerError($message = ''): self
    {
        $msg = $message ? 'internal server error (' . $message . ')' : 'internal server error';
        return new self(false, 500, $msg, null, null);
    }

    public static function notFound($message = ''): self
    {
        $msg = $message ? $message : 'not found';
        return new self(false, 404, $msg, null, null);
    }

    public static function unauthorized($message = ''): self
    {
        $msg = $message ? $message : 'unauthorized';
        return new self(false, 401, $msg, null, null);
    }

    public static function badRequest($message = '', $data = null): self
    {
        $msg = $message ? $message : 'bad request';
        return new self(false, 400, $msg, $data, null);
    }

    public static function pageExpired($message = '', $data = null): self
    {
        $msg = $message ? $message : 'page expired';
        return new self(false, 419, $msg, $data, null);
    }

    public static function unprocessableEntity($data = null, $message = ''): self
    {
        $msg = $message ? 'Unprocessable Entity (' . $message . ')' : 'Unprocessable Entity';
        return new self(false, 422, $msg, $data, null);
    }
}
