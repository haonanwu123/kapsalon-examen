<?php

namespace App\Entity;

use App\Repository\AfspraakRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AfspraakRepository::class)]
class Afspraak
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'afspraaks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $klant = null;

    #[ORM\ManyToOne(inversedBy: 'afspraaks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $medewerker = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getKlant(): ?User
    {
        return $this->klant;
    }

    public function setKlant(?User $klant): static
    {
        $this->klant = $klant;

        return $this;
    }

    public function getMedewerker(): ?User
    {
        return $this->medewerker;
    }

    public function setMedewerker(?User $medewerker): static
    {
        $this->medewerker = $medewerker;

        return $this;
    }
}
