<?php

namespace App\Commons\File;

use Illuminate\Http\UploadedFile;
use Ramsey\Uuid\Uuid;

class FileUploadService
{
    private UploadedFile $file;
    private string $targetPath;

    /**
     * FileUpload constructor.
     * @param UploadedFile $file
     * @param string $targetPath
     */
    public function __construct($file, $targetPath)
    {
        $this->file = $file;
        $this->targetPath = $targetPath;
    }

    public function upload(): FileUploadResponse
    {
        $response = new FileUploadResponse();
        try {
            $file = $this->getFile();
            $extension = $file->getClientOriginalExtension();
            $fileName = Uuid::uuid4()->toString() . '.' . $extension;
            $path = $this->file->storeAs($this->targetPath, $fileName, 'public');
            $urlFileName = '/storage/' . $path;
            $response->setSuccess(true)
                ->setMessage('success')
                ->setFileName($urlFileName);
        } catch (\Exception $e) {
            $response->setSuccess(false)
                ->setMessage($e->getMessage())
                ->setFileName(null);
        }
        return $response;
    }

    /**
     * Get the value of file
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set the value of file
     *
     * @return  self
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get the value of targetPath
     */
    public function getTargetPath()
    {
        return $this->targetPath;
    }

    /**
     * Set the value of targetPath
     *
     * @return  self
     */
    public function setTargetPath($targetPath)
    {
        $this->targetPath = $targetPath;

        return $this;
    }
}
