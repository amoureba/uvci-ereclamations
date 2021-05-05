<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AcademicYearRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GreaterThan;

/**
 * @ORM\Entity(repositoryClass=AcademicYearRepository::class)
 */
class AcademicYear
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
     * @ORM\Column(type="date")
     */
    private $startDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\OneToMany(targetEntity=Semester::class, mappedBy="academicYear")
     */
    private $semesters;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $archived;

    public function __toString(): string
    {
        return $this->coded;
    }

    public function __construct()
    {
        $this->semesters = new ArrayCollection();
        $this->registrations = new ArrayCollection();
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

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return Collection|Semester[]
     */
    public function getSemesters(): Collection
    {
        return $this->semesters;
    }

    public function addSemester(Semester $semester): self
    {
        if (!$this->semesters->contains($semester)) {
            $this->semesters[] = $semester;
            $semester->setAcademicYear($this);
        }

        return $this;
    }

    public function removeSemester(Semester $semester): self
    {
        if ($this->semesters->removeElement($semester)) {
            // set the owning side to null (unless already changed)
            if ($semester->getAcademicYear() === $this) {
                $semester->setAcademicYear(null);
            }
        }

        return $this;
    }

    public function getArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(?bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }

}
