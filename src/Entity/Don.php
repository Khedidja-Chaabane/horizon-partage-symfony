<?php

namespace App\Entity;

use App\Repository\DonRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DonRepository::class)]
class Don
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $montant = null;

    #[ORM\ManyToOne(inversedBy: 'dons')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $donateur = null;


    #[ORM\Column]
    private ?\DateTimeImmutable $dateDon = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDonateur(): ?User
    {
        return $this->donateur;
    }

    public function setDonateur(?User $donateur): static
    {
        $this->donateur = $donateur;

        return $this;
    }

    public function getDateDon(): ?\DateTimeImmutable
    {
        return $this->dateDon;
    }

    public function setDateDon(\DateTimeImmutable $dateDon): static
    {
        $this->dateDon = $dateDon;

        return $this;
    }
}
