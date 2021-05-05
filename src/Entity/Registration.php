<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RegistrationRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=RegistrationRepository::class)
 * @UniqueEntity(
 *  fields={"user", "level", "specialty", "semester"},
 *  message="Cette inscription existe déjà dans la base de données !"
 * )
 */
class Registration
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="registrations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Level::class, inversedBy="registrations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity=Specialty::class, inversedBy="registrations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $specialty;

    /**
     * @ORM\ManyToOne(targetEntity=Semester::class, inversedBy="registrations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $semester;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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

    public function getSpecialty(): ?Specialty
    {
        return $this->specialty;
    }

    public function setSpecialty(?Specialty $specialty): self
    {
        $this->specialty = $specialty;

        return $this;
    }

    public function getSemester(): ?Semester
    {
        return $this->semester;
    }

    public function setSemester(?Semester $semester): self
    {
        $this->semester = $semester;

        return $this;
    }
}
