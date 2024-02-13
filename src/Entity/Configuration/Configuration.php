<?php

namespace App\Entity\Configuration;

use App\Repository\Configuration\ConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigurationRepository::class)]
class Configuration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $number_men = null;

    #[ORM\Column(nullable: true)]
    private ?int $number_women = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumberMen(): ?int
    {
        return $this->number_men;
    }

    public function setNumberMen(?int $number_men): static
    {
        $this->number_men = $number_men;

        return $this;
    }

    public function getNumberWomen(): ?int
    {
        return $this->number_women;
    }

    public function setNumberWomen(?int $number_women): static
    {
        $this->number_women = $number_women;

        return $this;
    }
}