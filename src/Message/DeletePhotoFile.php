<?php

namespace App\Message;

class DeletePhotoFile
{
    public function __construct(private string $fileName)
    {

    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }
}
