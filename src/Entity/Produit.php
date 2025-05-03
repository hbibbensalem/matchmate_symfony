<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Commande;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity]
class Produit
{


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_produit", type: "integer")]
    private ?int $idProduit = null;

    #[ORM\Column(name: "nom_produit", type: "string", length: 110)]
    #[Assert\NotBlank(message: "Le nom du produit est obligatoire")]
    #[Assert\Length(
        min: 3,
        max: 110,
        minMessage: "Le nom doit faire au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z][a-zA-Z0-9 ]*$/",
        message: "Le nom doit commencer par une lettre et ne peut contenir que des lettres, chiffres et espaces"
    )]
    private string $nomProduit;
    
    #[ORM\Column(name: "description_produit", type: "text")]
    #[Assert\NotBlank(message: "La description est obligatoire")]
    #[Assert\Length(
        min: 10,
        minMessage: "La description doit faire au moins {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: "/^[\p{L}\p{N}\s.,;:!?'\"()\-@#&=+]+$/u",
        message: "La description contient des caractères non autorisés"
    )]
    private string $descriptionProduit;
    
    #[ORM\Column(name: "prix_produit", type: "integer")]
    #[Assert\NotBlank(message: "Le prix est obligatoire")]
    #[Assert\PositiveOrZero(message: "Le prix ne peut pas être négatif")]
    private int $prixProduit;
    
    #[ORM\Column(name: "quantite_produit", type: "integer")]
    #[Assert\NotBlank(message: "La quantité est obligatoire")]
    #[Assert\PositiveOrZero(message: "La quantité ne peut pas être négative")]
    private int $quantiteProduit;
    
  #[ORM\Column(name: "image_produit", type: "string", length: 255)]
private string $imageProduit;
    
    #[ORM\Column(name: "ref_produit", type: "string", length: 255, unique: true)]
    private ?string $refProduit = null;
  
    

    public function getIdProduit()
    {
        return $this->idProduit;
    }

    public function setIdProduit($value)
    {
        $this->idProduit = $value;
    }

    public function getNomProduit()
    {
        return $this->nomProduit;
    }

    public function setNomProduit($value)
    {
        $this->nomProduit = $value;
    }

    public function getDescriptionProduit()
    {
        return $this->descriptionProduit;
    }

    public function setDescriptionProduit($value)
    {
        $this->descriptionProduit = $value;
    }

    public function getPrixProduit()
    {
        return $this->prixProduit;
    }

    public function setPrixProduit($value)
    {
        $this->prixProduit = $value;
    }

    public function getQuantiteProduit()
    {
        return $this->quantiteProduit;
    }

    public function setQuantiteProduit($value)
    {
        $this->quantiteProduit = $value;
    }

    public function getImageProduit()
    {
        return $this->imageProduit;
    }

    public function setImageProduit($value)
    {
        $this->imageProduit = $value;
    }

    public function getRefProduit()
    {
        return $this->refProduit;
    }

    public function setRefProduit($value)
    {
        $this->refProduit = $value;
    }

    #[ORM\OneToMany(mappedBy: "produit", targetEntity: Commande::class)]
    private Collection $commandes;

    public function __construct()
    {
    $this->commandes = new ArrayCollection();
    }

    public function getOrderCount(): int
    {
        return $this->commandes->count();
    }

        public function getCommandes(): Collection
        {
            return $this->commandes;
        }
        public function addCommande(Commande $commande): self
        {
            if (!$this->commandes->contains($commande)) {
                $this->commandes->add($commande);
                $commande->setProduit($this);
            }
            return $this;
        }
        
        public function removeCommande(Commande $commande): self
        {
            if ($this->commandes->removeElement($commande)) {
                if ($commande->getProduit() === $this) {
                    $commande->setProduit(null);
                }
            }
            return $this;
        }

}
