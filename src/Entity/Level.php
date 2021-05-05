<?php

namespace App\Entity;

use App\Entity\MatterSpecialty;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\LevelRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=LevelRepository::class)
 *  @UniqueEntity(
 *  fields={"coded"},
 *  message="Ce niveau existe déjà dans la base des niveaux!"
 * )
 */
class Level
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $coded;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $wording;

    /**
     * @ORM\OneToMany(targetEntity=MatterSpecialty::class, mappedBy="level", orphanRemoval=true)
     */
    private $matterSpecialties;

    /**
     * @ORM\OneToMany(targetEntity=Registration::class, mappedBy="level", orphanRemoval=true)
     */
    private $registrations;

    /**
     * @ORM\OneToMany(targetEntity=Exam::class, mappedBy="level", orphanRemoval=true)
     */
    private $exams;

    public function __toString(): string
    {
        return $this->wording;
    }

    public function __construct()
    {
        $this->matterSpecialties = new ArrayCollection();
        $this->registrations = new ArrayCollection();
        $this->exams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWording(): ?string
    {
        return $this->wording;
    }

    public function setWording(string $wording): self
    {
        $this->wording = $wording;

        return $this;
    }

    public function getCoded(): ?string
    {
        return $this->coded;
    }

    public function setCoded(string $coded): self
    {
        $this->coded = $coded;

        return $this;
    }

    /**
     * @return Collection|MatterSpecialty[]
     */
    public function getMatterSpecialties(): Collection
    {
        return $this->matterSpecialties;
    }

    public function addMatterSpecialty(MatterSpecialty $matterSpecialty): self
    {
        if (!$this->matterSpecialties->contains($matterSpecialty)) {
            $this->matterSpecialties[] = $matterSpecialty;
            $matterSpecialty->setLevel($this);
        }

        return $this;
    }

    public function removeMatterSpecialty(MatterSpecialty $matterSpecialty): self
    {
        if ($this->matterSpecialties->removeElement($matterSpecialty)) {
            // set the owning side to null (unless already changed)
            if ($matterSpecialty->getLevel() === $this) {
                $matterSpecialty->setLevel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Registration[]
     */
    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }

    public function addRegistration(Registration $registration): self
    {
        if (!$this->registrations->contains($registration)) {
            $this->registrations[] = $registration;
            $registration->setLevel($this);
        }

        return $this;
    }

    public function removeRegistration(Registration $registration): self
    {
        if ($this->registrations->removeElement($registration)) {
            // set the owning side to null (unless already changed)
            if ($registration->getLevel() === $this) {
                $registration->setLevel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Exam[]
     */
    public function getExams(): Collection
    {
        return $this->exams;
    }

    public function addExam(Exam $exam): self
    {
        if (!$this->exams->contains($exam)) {
            $this->exams[] = $exam;
            $exam->setLevel($this);
        }

        return $this;
    }

    public function removeExam(Exam $exam): self
    {
        if ($this->exams->removeElement($exam)) {
            // set the owning side to null (unless already changed)
            if ($exam->getLevel() === $this) {
                $exam->setLevel(null);
            }
        }

        return $this;
    }
}
