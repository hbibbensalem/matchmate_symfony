<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Reclamation;

#[ORM\Entity]
class Support
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_s;

    #[ORM\Column(type: "string", length: 255)]
    private string $nom_responsable;

    #[ORM\Column(type: "integer")]
    private int $num_tel;

    #[ORM\Column(type: "string", length: 255)]
    private string $domaine;

    public function getId_s()
    {
        return $this->id_s;
    }

    public function setId_s($value)
    {
        $this->id_s = $value;
    }

    public function getNom_responsable()
    {
        return $this->nom_responsable;
    }

    public function setNom_responsable($value)
    {
        $this->nom_responsable = $value;
    }

    public function getNum_tel()
    {
        return $this->num_tel;
    }

    public function setNum_tel($value)
    {
        $this->num_tel = $value;
    }

    public function getDomaine()
    {
        return $this->domaine;
    }

    public function setDomaine($value)
    {
        $this->domaine = $value;
    }

    #[ORM\OneToMany(mappedBy: "id_s", targetEntity: Reclamation::class)]
    private Collection $reclamations;

        public function getReclamations(): Collection
        {
            return $this->reclamations;
        }
    
        public function addReclamation(Reclamation $reclamation): self
        {
            if (!$this->reclamations->contains($reclamation)) {
                $this->reclamations[] = $reclamation;
                $reclamation->setId_s($this);
            }
    
            return $this;
        }
    
        public function removeReclamation(Reclamation $reclamation): self
        {
            if ($this->reclamations->removeElement($reclamation)) {
                // set the owning side to null (unless already changed)
                if ($reclamation->getId_s() === $this) {
                    $reclamation->setId_s(null);
                }
            }
    
            return $this;
        }
}
