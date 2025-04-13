<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use App\Entity\Listinscri;

#[ORM\Entity]
class Match1
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "match1s")]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user', onDelete: 'CASCADE')]
    private User $id_user;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date;

    #[ORM\Column(type: "string", length: 255)]
    private string $heure;

    #[ORM\Column(type: "string", length: 255)]
    private string $localisation;

    #[ORM\Column(type: "string", length: 255)]
    private string $terrain;

    #[ORM\Column(type: "integer")]
    private int $nb_personne;

    #[ORM\Column(type: "string", length: 255)]
    private string $description;

    #[ORM\Column(type: "string", length: 255)]
    private string $typesport;

    #[ORM\Column(type: "string", length: 255)]
    private string $statut;

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

    #[ORM\OneToMany(mappedBy: "matchId", targetEntity: Listinscri::class)]
    private Collection $listinscris;

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
                // set the owning side to null (unless already changed)
                if ($listinscri->getMatchId() === $this) {
                    $listinscri->setMatchId(null);
                }
            }
    
            return $this;
        }
}
