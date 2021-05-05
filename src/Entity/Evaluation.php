<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\EvaluationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=EvaluationRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Evaluation
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
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $wording;

    /**
     * @ORM\ManyToOne(targetEntity=Semester::class, inversedBy="evaluations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $semester;

    /**
     * @ORM\ManyToOne(targetEntity=Matter::class, inversedBy="evaluations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $matter;

    /**
     * @ORM\Column(type="boolean")
     */
    private $archived;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Claim::class, mappedBy="evaluation")
     */
    private $claims;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\PrePersist
     * @return void
     */
    public function prePersist()
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }
    }

    /**
     * @param User $author
     * @return claim|null
     */
    public function getClaimFromAuthor(User $author){
        foreach($this->claims as $claim){
            if($claim->getAuthor() == $author) return $claim;
        }
        return null;  
    }

    public function __toString(): string
    {
        return $this->wording.'-'.$this->matter->getCoded().'-'.$this->semester->getSlug();
    }

    public function __construct()
    {
        $this->claims = new ArrayCollection();
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

    public function getArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

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

    public function getMatter(): ?Matter
    {
        return $this->matter;
    }

    public function setMatter(?Matter $matter): self
    {
        $this->matter = $matter;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|Claim[]
     */
    public function getClaims(): Collection
    {
        return $this->claims;
    }

    public function addClaim(Claim $claim): self
    {
        if (!$this->claims->contains($claim)) {
            $this->claims[] = $claim;
            $claim->setEvaluation($this);
        }

        return $this;
    }

    public function removeClaim(Claim $claim): self
    {
        if ($this->claims->removeElement($claim)) {
            // set the owning side to null (unless already changed)
            if ($claim->getEvaluation() === $this) {
                $claim->setEvaluation(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of type
     */ 
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */ 
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

}
