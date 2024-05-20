<?php

namespace App\Entity;

use App\Repository\BehandelingobjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BehandelingobjectRepository::class)]
class Behandelingobject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $img = null;

    /**
     * @var Collection<int, Behandeling>
     */
    #[ORM\OneToMany(targetEntity: Behandeling::class, mappedBy: 'behandelingobject')]
    private Collection $behandelings;

    public function __construct()
    {
        $this->behandelings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): static
    {
        $this->img = $img;

        return $this;
    }

    /**
     * @return Collection<int, Behandeling>
     */
    public function getBehandelings(): Collection
    {
        return $this->behandelings;
    }

    public function addBehandeling(Behandeling $behandeling): static
    {
        if (!$this->behandelings->contains($behandeling)) {
            $this->behandelings->add($behandeling);
            $behandeling->setBehandelingobject($this);
        }

        return $this;
    }

    public function removeBehandeling(Behandeling $behandeling): static
    {
        if ($this->behandelings->removeElement($behandeling)) {
            // set the owning side to null (unless already changed)
            if ($behandeling->getBehandelingobject() === $this) {
                $behandeling->setBehandelingobject(null);
            }
        }

        return $this;
    }
}
