<?php

namespace App\Entity\Voting;

use App\Entity\Account\User;
use App\Repository\Voting\VoteResultRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoteResultRepository::class)]
class VoteResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'voteResults')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vote $vote = null;

    #[ORM\ManyToOne(inversedBy: 'voteResult')]
    private ?User $responsible;

    #[ORM\Column(nullable: true)]
    private ?bool $isVotedOn = null;

    #[ORM\ManyToOne(inversedBy: 'voteResults')]
    private ?Candidat $candidat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVote(): ?Vote
    {
        return $this->vote;
    }

    public function setVote(?Vote $vote): static
    {
        $this->vote = $vote;

        return $this;
    }

    public function getResponsible(): ?Vote
    {
        return $this->responsible;
    }

    public function setResponsible(?User $responsible): static
    {
        $this->responsible = $responsible;

        return $this;
    }

    public function isIsVotedOn(): ?bool
    {
        return $this->isVotedOn;
    }

    public function setIsVotedOn(?bool $isVotedOn): static
    {
        $this->isVotedOn = $isVotedOn;

        return $this;
    }

    public function getCandidat(): ?Candidat
    {
        return $this->candidat;
    }

    public function setCandidat(?Candidat $candidat): static
    {
        $this->candidat = $candidat;

        return $this;
    }
}
