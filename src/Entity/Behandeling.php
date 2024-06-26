<?php

namespace App\Entity;

use App\Repository\BehandelingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BehandelingRepository::class)]
class Behandeling
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\ManyToOne(inversedBy: 'behandelings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Behandelingobject $behandelingobject = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getBehandelingobject(): ?Behandelingobject
    {
        return $this->behandelingobject;
    }

    public function setBehandelingobject(?Behandelingobject $behandelingobject): static
    {
        $this->behandelingobject = $behandelingobject;

        return $this;
    }
}
