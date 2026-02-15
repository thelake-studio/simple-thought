<?php

namespace App\Entity;

use App\Repository\GoalLogRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GoalLogRepository::class)]
class GoalLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'La fecha es obligatoria.')]
    #[Assert\LessThanOrEqual('today', message: 'No puedes registrar progreso en una fecha futura.')]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Debes indicar un valor de progreso.')]
    #[Assert\Positive(message: 'El valor del progreso debe ser un número positivo.')]
    private ?int $value = null;

    #[ORM\ManyToOne(inversedBy: 'goalLogs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'El registro debe estar vinculado a un objetivo.')]
    private ?Goal $goal = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getGoal(): ?Goal
    {
        return $this->goal;
    }

    public function setGoal(?Goal $goal): static
    {
        $this->goal = $goal;

        return $this;
    }
}
