<?php

namespace App\Entity\Voting;

use App\Repository\Voting\CandidatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidatRepository::class)]
class Candidat
{

    //User civility
    public const CANDIDAT_CIVILITY_MR  = 'Mr';
    public const CANDIDAT_CIVILITY_MME = 'Mme';

    public const CANDIDAT_CIVILITY_LIST   = [
        self::CANDIDAT_CIVILITY_MR  => 'Monsieur',
        self::CANDIDAT_CIVILITY_MME => 'Madame'
    ] ;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = '';

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;
    
    #[ORM\Column(length: 250, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(length: 255)]
    private ?string $civility = null;
    
    
    #[ORM\Column]
    private ?int $status = 1;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $number = null;

    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numberid = null;

    #[ORM\OneToMany(mappedBy: 'candidat', targetEntity: VoteResult::class)]
    private Collection $voteResults;

    public function __construct()
    {
        $this->voteResults = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): string
    {
        return "$this->firstname $this->lastname";
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname = ''): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getCivility(): ?string
    {
        return $this->civility;
    }

    public function setCivility(string $civility): static
    {
        $this->civility = $civility;

        return $this;
    }
    
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getNumberId(): ?string
    {
        return $this->numberid;
    }

    public function setNumberId(string $numberid): static
    {
        $this->numberid = $numberid;

        return $this;
    }

    /**
     * @return Collection<int, VoteResult>
     */
    public function getVoteResults(): Collection
    {
        return $this->voteResults;
    }

    public function addVoteResult(VoteResult $voteResult): static
    {
        if (!$this->voteResults->contains($voteResult)) {
            $this->voteResults->add($voteResult);
            $voteResult->setCandidat($this);
        }

        return $this;
    }

    public function removeVoteResult(VoteResult $voteResult): static
    {
        if ($this->voteResults->removeElement($voteResult)) {
            // set the owning side to null (unless already changed)
            if ($voteResult->getCandidat() === $this) {
                $voteResult->setCandidat(null);
            }
        }

        return $this;
    }
}
