<?php

namespace App\Entity;

use App\Repository\GoalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GoalRepository::class)]
class Goal
{
    public const TYPE_STREAK = 'STREAK';
    public const TYPE_SUM = 'SUM';

    public const PERIOD_DAILY = 'DAILY';
    public const PERIOD_WEEKLY = 'WEEKLY';
    public const PERIOD_MONTHLY = 'MONTHLY';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 20)]
    private ?string $type = null;

    #[ORM\Column(length: 20)]
    private ?string $period = null;

    #[ORM\Column(nullable: true)]
    private ?int $targetValue = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'goals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, GoalLog>
     */
    #[ORM\OneToMany(targetEntity: GoalLog::class, mappedBy: 'goal', orphanRemoval: true)]
    private Collection $goalLogs;

    public function __construct()
    {
        $this->goalLogs = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getPeriod(): ?string
    {
        return $this->period;
    }

    public function setPeriod(string $period): static
    {
        $this->period = $period;

        return $this;
    }

    public function getTargetValue(): ?int
    {
        return $this->targetValue;
    }

    public function setTargetValue(?int $targetValue): static
    {
        $this->targetValue = $targetValue;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

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

    /**
     * @return Collection<int, GoalLog>
     */
    public function getGoalLogs(): Collection
    {
        return $this->goalLogs;
    }

    public function addGoalLog(GoalLog $goalLog): static
    {
        if (!$this->goalLogs->contains($goalLog)) {
            $this->goalLogs->add($goalLog);
            $goalLog->setGoal($this);
        }

        return $this;
    }

    public function removeGoalLog(GoalLog $goalLog): static
    {
        if ($this->goalLogs->removeElement($goalLog)) {
            // set the owning side to null (unless already changed)
            if ($goalLog->getGoal() === $this) {
                $goalLog->setGoal(null);
            }
        }

        return $this;
    }
}
