<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\SpecialtyRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=SpecialtyRepository::class)
 * @UniqueEntity(
 *  fields={"coded"},
 *  message="Cette spécialité existe déjà dans la base de données !"
 * )
 */
class Specialty
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
     * @ORM\OneToMany(targetEntity=MatterSpecialty::class, mappedBy="specialty", orphanRemoval=true)
     */
    private $matterSpecialties;

    /**
     * @ORM\OneToMany(targetEntity=Registration::class, mappedBy="specialty", orphanRemoval=true)
     */
    private $registrations;

    /**
     * @ORM\OneToMany(targetEntity=Exam::class, mappedBy="specialty", orphanRemoval=true)
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

    public function getCoded(): ?string
    {
        return $this->coded;
    }

    public function setCoded(string $coded): self
    {
        $this->coded = $coded;

        return $this;
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
            $matterSpecialty->setSpecialty($this);
        }

        return $this;
    }

    public function removeMatterSpecialty(MatterSpecialty $matterSpecialty): self
    {
        if ($this->matterSpecialties->removeElement($matterSpecialty)) {
            // set the owning side to null (unless already changed)
            if ($matterSpecialty->getSpecialty() === $this) {
                $matterSpecialty->setSpecialty(null);
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
            $registration->setSpecialty($this);
        }

        return $this;
    }

    public function removeRegistration(Registration $registration): self
    {
        if ($this->registrations->removeElement($registration)) {
            // set the owning side to null (unless already changed)
            if ($registration->getSpecialty() === $this) {
                $registration->setSpecialty(null);
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
            $exam->setSpecialty($this);
        }

        return $this;
    }

    public function removeExam(Exam $exam): self
    {
        if ($this->exams->removeElement($exam)) {
            // set the owning side to null (unless already changed)
            if ($exam->getSpecialty() === $this) {
                $exam->setSpecialty(null);
            }
        }

        return $this;
    }
}
