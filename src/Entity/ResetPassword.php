<?php

namespace App\Entity;

class ResetPassword
{

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }
}
