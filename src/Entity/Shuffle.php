<?php

namespace App\Entity;

use App\Repository\ShuffleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShuffleRepository::class)]
class Shuffle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Game $game = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $wish = null;

    #[ORM\ManyToOne]
    private ?User $giver = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

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

    public function getWish(): ?string
    {
        return $this->wish;
    }

    public function setWish(string $wish): static
    {
        $this->wish = $wish;

        return $this;
    }

    public function getGiver(): ?User
    {
        return $this->giver;
    }

    public function setGiver(?User $giver): static
    {
        $this->giver = $giver;

        return $this;
    }
}
