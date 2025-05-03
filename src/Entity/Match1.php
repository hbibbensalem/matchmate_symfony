<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Listinscri;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[UniqueEntity(
    fields: ['date', 'heure', 'localisation'],
    errorPath: 'heure',
    message: 'Un match existe déjà à cette date, heure et lieu.'
)]
class Match1
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "match1s")]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user', onDelete: 'CASCADE')]
    private User $id_user;

    #[ORM\Column(type: "date")]
    #[Assert\NotBlank(message: "La date est obligatoire.")]
    #[Assert\Type(\DateTimeInterface::class)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: "string", length: 255)]
#[Assert\NotBlank(message: "L'heure est obligatoire.")]
#[Assert\Regex(
    pattern: "/^(?:[01]\d|2[0-3]):[0-5]\d$/",
    message: "L'heure doit être au format HH:MM (24h)."
)]
private ?string $heure = null; // Rend la propriété nullable

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "La localisation est obligatoire.")]
    #[Assert\Length(max: 255)]
    private string $localisation;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Le terrain est obligatoire.")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le terrain doit faire au moins {{ limit }} caractères.",
        maxMessage: "Le terrain ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $terrain = null; // Notez le ?string et nullable: true

    #[ORM\Column(type: "integer")]
    #[Assert\Range(
        min: 2,
        max: 20,
        notInRangeMessage: "Le nombre de personnes doit être entre {{ min }} et {{ max }}."
    )]
    private int $nb_personne = 2;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(min: 10, max: 255)]
    private string $description;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Le type de sport est obligatoire.")]
    #[Assert\Length(min: 3, max: 255)]
    private string $typesport;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\Choice(choices: ["en attente", "Validé", "Annulé", "confirmé"], message: "Statut invalide.")]
private string $statut = 'en attente'; // Uniformiser la casse

    #[ORM\OneToMany(mappedBy: "matchId", targetEntity: Listinscri::class)]
    private Collection $listinscris;

    public function __construct()
    {
        $this->listinscris = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getId_user()
    {
        return $this->id_user;
    }

    public function setId_user($value)
    {
        $this->id_user = $value;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($value)
    {
        $this->date = $value;
    }

    public function getHeure()
    {
        return $this->heure;
    }

    public function setHeure($value)
    {
        $this->heure = $value;
    }

    public function getLocalisation()
    {
        return $this->localisation;
    }

    public function setLocalisation($value)
    {
        $this->localisation = $value;
    }

    public function getTerrain()
    {
        return $this->terrain;
    }

    public function setTerrain($value)
    {
        $this->terrain = $value;
    }

    public function getNb_personne()
    {
        return $this->nb_personne;
    }

    public function setNb_personne($value)
    {
        $this->nb_personne = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getTypesport()
    {
        return $this->typesport;
    }

    public function setTypesport($value)
    {
        $this->typesport = $value;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut($value)
    {
        $this->statut = $value;
    }

    public function getListinscris(): Collection
    {
        return $this->listinscris;
    }

    public function addListinscri(Listinscri $listinscri): self
    {
        if (!$this->listinscris->contains($listinscri)) {
            $this->listinscris[] = $listinscri;
            $listinscri->setMatchId($this);
        }

        return $this;
    }

    public function removeListinscri(Listinscri $listinscri): self
    {
        if ($this->listinscris->removeElement($listinscri)) {
            if ($listinscri->getMatchId() === $this) {
                $listinscri->setMatchId(null);
            }
        }

        return $this;
    }
}
