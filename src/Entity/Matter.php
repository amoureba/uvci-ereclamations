<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\MatterRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=MatterRepository::class)
 * @UniqueEntity(
 *  fields={"coded"},
 *  message="Cette matière existe déjà dans la base des matières !"
 * )
 */
class Matter
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
     * @ORM\OneToMany(targetEntity=Evaluation::class, mappedBy="matter")
     */
    private $evaluations;

    /**
     * @ORM\OneToMany(targetEntity=MatterSpecialty::class, mappedBy="matter", orphanRemoval=true)
     */
    private $matterSpecialties;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="matters")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=Examination::class, mappedBy="matter")
     */
    private $examinations;

    public function __toString(): string
    {
        return $this->wording;
    }

    public function __construct()
    {
        $this->evaluations = new ArrayCollection();
        $this->matterSpecialties = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->examinations = new ArrayCollection();
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
     * @return Collection|Evaluation[]
     */
    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function addEvaluation(Evaluation $evaluation): self
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations[] = $evaluation;
            $evaluation->setMatter($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): self
    {
        if ($this->evaluations->removeElement($evaluation)) {
            // set the owning side to null (unless already changed)
            if ($evaluation->getMatter() === $this) {
                $evaluation->setMatter(null);
            }
        }

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
            $matterSpecialty->setMatter($this);
        }

        return $this;
    }

    public function removeMatterSpecialty(MatterSpecialty $matterSpecialty): self
    {
        if ($this->matterSpecialties->removeElement($matterSpecialty)) {
            // set the owning side to null (unless already changed)
            if ($matterSpecialty->getMatter() === $this) {
                $matterSpecialty->setMatter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addMatter($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeMatter($this);
        }

        return $this;
    }

    /**
     * @return Collection|Examination[]
     */
    public function getExaminations(): Collection
    {
        return $this->examinations;
    }

    public function addExamination(Examination $examination): self
    {
        if (!$this->examinations->contains($examination)) {
            $this->examinations[] = $examination;
            $examination->setMatter($this);
        }

        return $this;
    }

    public function removeExamination(Examination $examination): self
    {
        if ($this->examinations->removeElement($examination)) {
            // set the owning side to null (unless already changed)
            if ($examination->getMatter() === $this) {
                $examination->setMatter(null);
            }
        }

        return $this;
    }

}
