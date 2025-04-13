<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Commande;

#[ORM\Entity]
class Produit
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_produit;

    #[ORM\Column(type: "string", length: 110)]
    private string $nom_produit;

    #[ORM\Column(type: "string", length: 110)]
    private string $description_produit;

    #[ORM\Column(type: "integer")]
    private int $prix_produit;

    #[ORM\Column(type: "integer")]
    private int $quantite_produit;

    #[ORM\Column(type: "string", length: 255)]
    private string $image_produit;

    #[ORM\Column(type: "string", length: 255)]
    private string $ref_produit;

    public function getId_produit()
    {
        return $this->id_produit;
    }

    public function setId_produit($value)
    {
        $this->id_produit = $value;
    }

    public function getNom_produit()
    {
        return $this->nom_produit;
    }

    public function setNom_produit($value)
    {
        $this->nom_produit = $value;
    }

    public function getDescription_produit()
    {
        return $this->description_produit;
    }

    public function setDescription_produit($value)
    {
        $this->description_produit = $value;
    }

    public function getPrix_produit()
    {
        return $this->prix_produit;
    }

    public function setPrix_produit($value)
    {
        $this->prix_produit = $value;
    }

    public function getQuantite_produit()
    {
        return $this->quantite_produit;
    }

    public function setQuantite_produit($value)
    {
        $this->quantite_produit = $value;
    }

    public function getImage_produit()
    {
        return $this->image_produit;
    }

    public function setImage_produit($value)
    {
        $this->image_produit = $value;
    }

    public function getRef_produit()
    {
        return $this->ref_produit;
    }

    public function setRef_produit($value)
    {
        $this->ref_produit = $value;
    }

    #[ORM\OneToMany(mappedBy: "idProduit", targetEntity: Commande::class)]
    private Collection $commandes;

        public function getCommandes(): Collection
        {
            return $this->commandes;
        }
    
        public function addCommande(Commande $commande): self
        {
            if (!$this->commandes->contains($commande)) {
                $this->commandes[] = $commande;
                $commande->setIdProduit($this);
            }
    
            return $this;
        }
    
        public function removeCommande(Commande $commande): self
        {
            if ($this->commandes->removeElement($commande)) {
                // set the owning side to null (unless already changed)
                if ($commande->getIdProduit() === $this) {
                    $commande->setIdProduit(null);
                }
            }
    
            return $this;
        }
}
