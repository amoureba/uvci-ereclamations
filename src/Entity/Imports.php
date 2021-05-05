<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Imports
{
    /**
     * @Assert\NotBlank()
     */
    private $file;
    public function getFile(): ?string
    {
        return $this->file;
    }
    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }
}
