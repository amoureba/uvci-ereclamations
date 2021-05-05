<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\MatterSpecialtyRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=MatterSpecialtyRepository::class)
 * @UniqueEntity(
 *  fields={"matter", "specialty", "level", "semester"},
 *  message="Cette liaison existe déjà dans la base de données !"
 * )
 */
class MatterSpecialty
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Matter::class, inversedBy="matterSpecialties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $matter;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Specialty::class, inversedBy="matterSpecialties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $specialty;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity=Level::class, inversedBy="matterSpecialties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $level;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $semester;

    public function getMatter(): ?Matter
    {
        return $this->matter;
    }

    public function setMatter(?Matter $matter): self
    {
        $this->matter = $matter;

        return $this;
    }

    public function getSpecialty(): ?Specialty
    {
        return $this->specialty;
    }

    public function setSpecialty(?Specialty $specialty): self
    {
        $this->specialty = $specialty;

        return $this;
    }

    public function getLevel(): ?Level
    {
        return $this->level;
    }

    public function setLevel(?Level $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getSemester(): ?string
    {
        return $this->semester;
    }

    public function setSemester(?string $semester): self
    {
        $this->semester = $semester;

        return $this;
    }
}
