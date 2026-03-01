<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entidad que representa una etiqueta (Tag) personalizada creada por el usuario.
 * Se utiliza para categorizar y organizar las entradas del diario mediante una relación ManyToMany.
 */
#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El nombre de la etiqueta es obligatorio.')]
    #[Assert\Length(max: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 7, nullable: true)]
    #[Assert\Regex(
        pattern: '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
        message: 'El color del tag debe ser un formato hexadecimal válido.'
    )]
    private ?string $color = null;

    #[ORM\ManyToOne(inversedBy: 'tags')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, Entry> Colección de entradas del diario que contienen esta etiqueta.
     */
    #[ORM\ManyToMany(targetEntity: Entry::class, mappedBy: 'tags')]
    private Collection $entries;

    /**
     * Inicializa la colección de entradas asociadas a la etiqueta.
     */
    public function __construct()
    {
        $this->entries = new ArrayCollection();
    }

    /**
     * @return int|null El identificador único de la etiqueta.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null El nombre descriptivo de la etiqueta.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name El nuevo nombre para la etiqueta.
     * @return static
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null El código hexadecimal del color asociado.
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @param string|null $color El código hexadecimal (ej. #FFFFFF).
     * @return static
     */
    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return User|null El usuario propietario de esta etiqueta.
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user El usuario al que pertenece la etiqueta.
     * @return static
     */
    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Obtiene la colección de entradas del diario vinculadas.
     * * @return Collection<int, Entry>
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    /**
     * Vincula una nueva entrada del diario a esta etiqueta.
     * * @param Entry $entry La entrada a vincular.
     * @return static
     */
    public function addEntry(Entry $entry): static
    {
        if (!$this->entries->contains($entry)) {
            $this->entries->add($entry);
            $entry->addTag($this);
        }

        return $this;
    }

    /**
     * Desvincula una entrada del diario de esta etiqueta.
     * * @param Entry $entry La entrada a eliminar de la relación.
     * @return static
     */
    public function removeEntry(Entry $entry): static
    {
        if ($this->entries->removeElement($entry)) {
            $entry->removeTag($this);
        }

        return $this;
    }
}
