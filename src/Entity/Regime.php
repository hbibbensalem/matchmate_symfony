<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use App\Entity\Suivi;

#[ORM\Entity]
class Regime
{

    #[ORM\Id]
    #[ORM\GeneratedValue] // Added to make it auto-increment
    #[ORM\Column(type: "integer")]
    private int $regime_id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "regimes")]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user', onDelete: 'CASCADE')]
    private User $id_user;

    #[ORM\Column(type: "string", length: 100)]
    private string $objectif;

    #[ORM\Column(type: "integer")]
    private int $calories_journalieres;

    #[ORM\Column(type: "integer")]
    private int $proteines;

    #[ORM\Column(type: "integer")]
    private int $glucides;

    #[ORM\Column(type: "integer")]
    private int $lipides;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date_debut;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date_fin;

    #[ORM\Column(type: "string", length: 50)]
    private string $statut;

    public function getRegimeId()
    {
        return $this->regime_id;
    }

    public function setRegimeId($value)
    {
        $this->regime_id = $value;
        return $this;
    }

    public function getIdUser(): User
    {
        return $this->id_user;
    }

    public function setIdUser(User $value): self
    {
        $this->id_user = $value;
        return $this;
    }

    public function getObjectif()
    {
        return $this->objectif;
    }

    public function setObjectif($value)
    {
        $this->objectif = $value;
    }

    public function getCaloriesJournalieres()
    {
        return $this->calories_journalieres;
    }

    public function setCaloriesJournalieres($value)
    {
        $this->calories_journalieres = $value;
    }

    public function getProteines()
    {
        return $this->proteines;
    }

    public function setProteines($value)
    {
        $this->proteines = $value;
    }

    public function getGlucides()
    {
        return $this->glucides;
    }

    public function setGlucides($value)
    {
        $this->glucides = $value;
    }

    public function getLipides()
    {
        return $this->lipides;
    }

    public function setLipides($value)
    {
        $this->lipides = $value;
    }

    public function getDateDebut(): \DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $value): self
    {
        $this->date_debut = $value;
        return $this;
    }

    public function getDateFin(): \DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $value): self
    {
        $this->date_fin = $value;
        return $this;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut($value)
    {
        $this->statut = $value;
    }

    #[ORM\OneToMany(mappedBy: "regime_id", targetEntity: Suivi::class)]
    private Collection $suivis;

        public function getSuivis(): Collection
        {
            return $this->suivis;
        }
    
        public function addSuivi(Suivi $suivi): self
        {
            if (!$this->suivis->contains($suivi)) {
                $this->suivis[] = $suivi;
                $suivi->setRegimeId($this);
            }
    
            return $this;
        }
    
        public function removeSuivi(Suivi $suivi): self
        {
            if ($this->suivis->removeElement($suivi)) {
                // set the owning side to null (unless already changed)
                if ($suivi->getRegimeId() === $this) {
                    $suivi->setRegimeId(null);
                }
            }
    
            return $this;
        }
}
