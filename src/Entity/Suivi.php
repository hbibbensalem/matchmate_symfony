<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Regime;

#[ORM\Entity]
class Suivi
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $suivi_id;

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "suivis")]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user', onDelete: 'CASCADE')]
    private User $id_user;

        #[ORM\ManyToOne(targetEntity: Regime::class, inversedBy: "suivis")]
    #[ORM\JoinColumn(name: 'regime_id', referencedColumnName: 'regime_id', onDelete: 'CASCADE')]
    private Regime $regime_id;

    #[ORM\Column(type: "float")]
    private float $poids;

    #[ORM\Column(type: "float")]
    private float $tour_de_taille;

    #[ORM\Column(type: "float")]
    private float $imc;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date_suivi;

    #[ORM\Column(type: "float")]
    private float $taille;

    public function getSuivi_id()
    {
        return $this->suivi_id;
    }

    public function setSuivi_id($value)
    {
        $this->suivi_id = $value;
    }

    public function getId_user()
    {
        return $this->id_user;
    }

    public function setId_user($value)
    {
        $this->id_user = $value;
    }

    public function getRegime_id()
    {
        return $this->regime_id;
    }

    public function setRegime_id($value)
    {
        $this->regime_id = $value;
    }

    public function getPoids()
    {
        return $this->poids;
    }

    public function setPoids($value)
    {
        $this->poids = $value;
    }

    public function getTour_de_taille()
    {
        return $this->tour_de_taille;
    }

    public function setTour_de_taille($value)
    {
        $this->tour_de_taille = $value;
    }

    public function getImc()
    {
        return $this->imc;
    }

    public function setImc($value)
    {
        $this->imc = $value;
    }

    public function getDate_suivi()
    {
        return $this->date_suivi;
    }

    public function setDate_suivi($value)
    {
        $this->date_suivi = $value;
    }

    public function getTaille()
    {
        return $this->taille;
    }

    public function setTaille($value)
    {
        $this->taille = $value;
    }
}
