<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entidad de Usuario que gestiona la identidad, autenticación y seguridad del sistema.
 * Implementa UserInterface para integrarse con el firewall de Symfony y
 * PasswordAuthenticatedUserInterface para el manejo de credenciales hash.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Ya existe una cuenta vinculada a este correo electrónico.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: 'El email no puede estar vacío.')]
    #[Assert\Email(message: 'El formato del email no es válido.')]
    private ?string $email = null;

    /**
     * @var list<string> Los roles asignados al usuario para el control de acceso.
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string La contraseña cifrada del usuario.
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Por favor, dinos cómo quieres que te llamemos.')]
    #[Assert\Length(
        min: 2,
        max: 20,
        minMessage: 'Tu nickname debe tener al menos {{ limit }} caracteres.',
        maxMessage: 'Tu nickname no puede tener más de {{ limit }} caracteres.'
    )]
    private ?string $nickname = null;

    #[ORM\Column(nullable: true)]
    private ?array $preferences = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Emotion> Colección de emociones personalizadas.
     */
    #[ORM\OneToMany(targetEntity: Emotion::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $emotions;

    /**
     * @var Collection<int, Entry> Colección de entradas del diario emocional.
     */
    #[ORM\OneToMany(targetEntity: Entry::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $entries;

    /**
     * @var Collection<int, Activity> Colección de actividades del catálogo.
     */
    #[ORM\OneToMany(targetEntity: Activity::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $activities;

    /**
     * @var Collection<int, Tag> Colección de etiquetas creadas.
     */
    #[ORM\OneToMany(targetEntity: Tag::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $tags;

    /**
     * @var Collection<int, Goal> Colección de objetivos definidos.
     */
    #[ORM\OneToMany(targetEntity: Goal::class, mappedBy: 'user')]
    private Collection $goals;

    /**
     * Inicializa las colecciones de las relaciones OneToMany.
     */
    public function __construct()
    {
        $this->emotions = new ArrayCollection();
        $this->entries = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->goals = new ArrayCollection();
    }

    /**
     * @return int|null Identificador único de la entidad.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null Correo electrónico del usuario.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email Nuevo correo electrónico.
     * @return static
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Identificador visual que representa al usuario ante el sistema de seguridad.
     *
     * @see UserInterface
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Obtiene los roles del usuario, garantizando siempre el ROLE_USER.
     *
     * @see UserInterface
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles Lista de roles asignados.
     * @return static
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Obtiene la contraseña hash de la base de datos.
     *
     * @see PasswordAuthenticatedUserInterface
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password Contraseña ya cifrada.
     * @return static
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Sobrescribe la serialización para asegurar que no se guarden hashes reales en la sesión.
     * Soporta el hashing CRC32C de Symfony 7.3+.
     * * @return array
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    /**
     * Elimina credenciales temporales sensibles tras la autenticación.
     */
    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated para ser eliminado en futuras versiones de Symfony.
    }

    /**
     * @return string|null Apodo público del usuario.
     */
    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    /**
     * @param string $nickname Nuevo apodo.
     * @return static
     */
    public function setNickname(string $nickname): static
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * @return array|null Preferencias de configuración del usuario.
     */
    public function getPreferences(): ?array
    {
        return $this->preferences;
    }

    /**
     * @param array|null $preferences Mapa de preferencias.
     * @return static
     */
    public function setPreferences(?array $preferences): static
    {
        $this->preferences = $preferences;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null Fecha en la que se creó la cuenta.
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeImmutable $createdAt Fecha de creación.
     * @return static
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Emotion>
     */
    public function getEmotions(): Collection
    {
        return $this->emotions;
    }

    /**
     * Añade una emoción al catálogo del usuario.
     * @param Emotion $emotion
     * @return static
     */
    public function addEmotion(Emotion $emotion): static
    {
        if (!$this->emotions->contains($emotion)) {
            $this->emotions->add($emotion);
            $emotion->setUser($this);
        }

        return $this;
    }

    /**
     * Elimina una emoción del catálogo.
     * @param Emotion $emotion
     * @return static
     */
    public function removeEmotion(Emotion $emotion): static
    {
        if ($this->emotions->removeElement($emotion)) {
            if ($emotion->getUser() === $this) {
                $emotion->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Entry>
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    /**
     * Vincula una entrada de diario al usuario.
     * @param Entry $entry
     * @return static
     */
    public function addEntry(Entry $entry): static
    {
        if (!$this->entries->contains($entry)) {
            $this->entries->add($entry);
            $entry->setUser($this);
        }

        return $this;
    }

    /**
     * Desvincula una entrada de diario.
     * @param Entry $entry
     * @return static
     */
    public function removeEntry(Entry $entry): static
    {
        if ($this->entries->removeElement($entry)) {
            if ($entry->getUser() === $this) {
                $entry->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    /**
     * Añade una actividad al catálogo del usuario.
     * @param Activity $activity
     * @return static
     */
    public function addActivity(Activity $activity): static
    {
        if (!$this->activities->contains($activity)) {
            $this->activities->add($activity);
            $activity->setUser($this);
        }

        return $this;
    }

    /**
     * Elimina una actividad del catálogo.
     * @param Activity $activity
     * @return static
     */
    public function removeActivity(Activity $activity): static
    {
        if ($this->activities->removeElement($activity)) {
            if ($activity->getUser() === $this) {
                $activity->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * Añade una etiqueta al catálogo del usuario.
     * @param Tag $tag
     * @return static
     */
    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->setUser($this);
        }

        return $this;
    }

    /**
     * Elimina una etiqueta del catálogo.
     * @param Tag $tag
     * @return static
     */
    public function removeTag(Tag $tag): static
    {
        if ($this->tags->removeElement($tag)) {
            if ($tag->getUser() === $this) {
                $tag->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Goal>
     */
    public function getGoals(): Collection
    {
        return $this->goals;
    }

    /**
     * Vincula un objetivo al usuario.
     * @param Goal $goal
     * @return static
     */
    public function addGoal(Goal $goal): static
    {
        if (!$this->goals->contains($goal)) {
            $this->goals->add($goal);
            $goal->setUser($this);
        }

        return $this;
    }

    /**
     * Desvincula un objetivo del usuario.
     * @param Goal $goal
     * @return static
     */
    public function removeGoal(Goal $goal): static
    {
        if ($this->goals->removeElement($goal)) {
            if ($goal->getUser() === $this) {
                $goal->setUser(null);
            }
        }

        return $this;
    }
}
