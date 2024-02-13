<?php

namespace App\Entity\Voting;

use App\Entity\Account\User;
use App\Entity\Voting\Vote;
use App\Repository\Voting\VoteResultRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoteResultRepository::class)]
class VoteResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'voteResult')]
    private ?Vote $vote;
    
    #[ORM\ManyToOne(inversedBy: 'voteResult')]
    private ?Candidat $candidat;
    
    #[ORM\ManyToOne(inversedBy: 'voteResult')]
    private ?User $responsible;

    #[ORM\Column(nullable: true)]
    private ?int $number = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(?int $number): static
    {
        $this->number = $number;

        return $this;
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
    
    public function getCandidat(): ?Vote
    {
        return $this->candidat;
    }

    public function setCandidat(?Candidat $candidat): static
    {
        $this->candidat = $candidat;

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
}
