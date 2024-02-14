<?php

namespace App\Entity\Voting;

use App\Entity\Account\User;
use App\Repository\Voting\VoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoteRepository::class)]
class Vote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $num = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isDead = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isWhite = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated = null;

    #[ORM\OneToMany(mappedBy: 'vote', targetEntity: VoteResult::class, orphanRemoval: true)]
    private Collection $voteResults;

    public function __construct()
    {
        $this->voteResults = new ArrayCollection();
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNum(): ?string
    {
        return $this->num;
    }

    public function setNum(string $num): static
    {
        $this->num = $num;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function isIsDead(): ?bool
    {
        return $this->isDead;
    }

    public function setIsDead(?bool $isDead): static
    {
        $this->isDead = $isDead;

        return $this;
    }

    public function isIsWhite(): ?bool
    {
        return $this->isWhite;
    }

    public function setIsWhite(?bool $isWhite): static
    {
        $this->isWhite = $isWhite;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): static
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated): static
    {
        $this->updated = $updated;

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
            $voteResult->setVote($this);
        }

        return $this;
    }

    public function removeVoteResult(VoteResult $voteResult): static
    {
        if ($this->voteResults->removeElement($voteResult)) {
            // set the owning side to null (unless already changed)
            if ($voteResult->getVote() === $this) {
                $voteResult->setVote(null);
            }
        }

        return $this;
    }
}
