<?php

namespace App\Commons\File;

class FileUploadResponse
{
    private bool $success;
    private string $message;
    private $fileName;

     public function __construct($success = false, $message = '', $fileName = null)
    {
        $this->success = $success;
        $this->message = $message;
        $this->fileName = $fileName;
    }

    /**
     * Get the value of success
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * Set the value of success
     *
     * @return  self
     */
    public function setSuccess($success)
    {
        $this->success = $success;

        return $this;
    }

    /**
     * Get the value of message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the value of message
     *
     * @return  self
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get the value of fileName
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set the value of fileName
     *
     * @return  self
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }
}
