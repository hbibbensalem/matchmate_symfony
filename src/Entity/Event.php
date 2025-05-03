<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Participation;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'event')]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $idevent = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'events')]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user', onDelete: 'CASCADE')]
    private ?User $id_user = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le titre ne peut pas être vide")]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Le titre doit faire au moins {{ limit }} caractères",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: '/^[^<>]*$/',
        message: "Le titre ne doit pas contenir de balises HTML"
    )]
    private string $titre;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(message: "La date ne peut pas être vide")]
    #[Assert\GreaterThan(
        value: "today",
        message: "La date doit être dans le futur"
    )]
    private \DateTimeInterface $date;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le lieu ne peut pas être vide")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le lieu doit faire au moins {{ limit }} caractères",
        maxMessage: "Le lieu ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: '/^[^<>]*$/',
        message: "Le lieu ne doit pas contenir de balises HTML"
    )]
    private string $lieu;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "L'URL de l'image ne peut pas être vide")]
    #[Assert\Url(message: "L'URL de l'image n'est pas valide")]
    private string $image_url;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: "La description ne peut pas être vide")]
    #[Assert\Length(
        min: 20,
        max: 2000,
        minMessage: "La description doit faire au moins {{ limit }} caractères",
    )]
    #[Assert\Regex(
        pattern: '/^[^<>]*$/',
        message: "La description ne doit pas contenir de balises HTML"
    )]
    private string $description;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: "La capacité ne peut pas être vide")]
    #[Assert\Positive(message: "La capacité doit être un nombre positif")]
    #[Assert\LessThan(
        value: 1000,
        message: "La capacité ne peut pas dépasser {{ value }}"
    )]
    private int $capacite;

    #[ORM\OneToMany(mappedBy: 'idevent', targetEntity: Participation::class, cascade: ['persist', 'remove'])]
    private Collection $participations;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
    }

    // Getters et Setters

    public function getIdevent(): ?int
    {
        return $this->idevent;
    }

    public function getIdUser(): ?User
    {
        return $this->id_user;
    }

    public function setIdUser(?User $id_user): self
    {
        $this->id_user = $id_user;
        return $this;
    }
    public function setIdevent(int $idevent): self
    {
        $this->idevent = $idevent;
        return $this;
    }
    
    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = strip_tags($titre);
        return $this;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getLieu(): string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = strip_tags($lieu);
        return $this;
    }

    public function getImageUrl(): string
    {
        return $this->image_url;
    }

    public function setImageUrl(string $image_url): self
    {
        $this->image_url = filter_var($image_url, FILTER_SANITIZE_URL);
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = strip_tags($description);
        return $this;
    }

    public function getCapacite(): int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): self
    {
        $this->capacite = $capacite;
        return $this;
    }

    /**
     * @return Collection|Participation[]
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): self
    {
        if (!$this->participations->contains($participation)) {
            $this->participations[] = $participation;
            $participation->setIdevent($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): self
    {
        if ($this->participations->removeElement($participation)) {
            if ($participation->getIdevent() === $this) {
                $participation->setIdevent(null);
            }
        }

        return $this;
    }

    // Méthode pour vérifier si l'événement est complet
    public function isFull(): bool
    {
        return count($this->participations) >= $this->capacite;
    }

    // Méthode pour obtenir le nombre de places restantes
    public function getRemainingPlaces(): int
    {
        return max(0, $this->capacite - count($this->participations));
    }
}