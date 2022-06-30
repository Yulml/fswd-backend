<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 100, options: ["default" => "blank_cover.jpg"])]
    private $cover;

    #[ORM\ManyToOne(targetEntity: Genre::class, inversedBy: 'games')]
    #[ORM\JoinColumn(nullable: false)]
    private $genre;

    #[ORM\ManyToOne(targetEntity: Platform::class, inversedBy: 'games')]
    #[ORM\JoinColumn(nullable: false)]
    private $platform;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: Owned::class)]
    private $owneds;

    public function __construct()
    {
        $this->owneds = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(string $cover): self
    {
        $this->cover = $cover;

        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getPlatform(): ?Platform
    {
        return $this->platform;
    }

    public function setPlatform(?Platform $platform): self
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * @return Collection<int, Owned>
     */
    public function getOwneds(): Collection
    {
        return $this->owneds;
    }

    public function addOwned(Owned $owned): self
    {
        if (!$this->owneds->contains($owned)) {
            $this->owneds[] = $owned;
            $owned->setGame($this);
        }

        return $this;
    }

    public function removeOwned(Owned $owned): self
    {
        if ($this->owneds->removeElement($owned)) {
            // set the owning side to null (unless already changed)
            if ($owned->getGame() === $this) {
                $owned->setGame(null);
            }
        }

        return $this;
    }    

    public function toArray(): array {
        return [
                'id' => $this->getId(),
                'cover' => $this->getCover(), 
                'name' => $this->getName(),
                'genre' => $this->getGenre(),// References table Genre, I get circular reference error
                'platform' => $this->getPlatform(),// References table Platform, I get circular reference error
        ];
    }
}
