<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Produit;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\DateTimeInterface;


#[ORM\Entity]
class Commande
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"id_commande",type: "integer")]
    private int $idCommande;

    #[ORM\Column(name:"date_commande",type: "datetime")]
    private \DateTimeInterface $dateCommande;

    #[ORM\Column(name:"quantite_commande",type: "integer")]
    private int $quantiteCommande;

    #[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: "commandes")]
    #[ORM\JoinColumn(name: "idProduit", referencedColumnName: "id_produit", nullable: false, onDelete: "CASCADE")]
    private ?Produit $produit = null;

    #[ORM\Column(name:"status_commande",type: "string", length: 50, options: ["default" => "EN ATTENTE"])]
    private string $statusCommande = 'EN ATTENTE';

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "commandes")]
    #[ORM\JoinColumn(name: "id_user", referencedColumnName: "id_user", nullable: false)]
    private ?User $user = null;

    public function getIdCommande()
    {
        return $this->idCommande;
    }

    public function setIdCommande($value)
    {
        $this->idCommande = $value;

    }

    public function __construct()
    {
        $this->dateCommande = new \DateTime(); // Initialisation automatique
    }

    public function setDateCommande(\DateTimeInterface $date): self
    {
        $this->dateCommande = $date;
        return $this;
    }
    public function getDateCommande(): \DateTimeInterface
    {
        return $this->dateCommande;
    }
    
 

    public function getQuantiteCommande()
    {
        return $this->quantiteCommande;

    }

    public function setQuantiteCommande($value)
    {
          $this->quantiteCommande = $value;
    return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }
    
    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;
        return $this;
    }

    public function getStatusCommande()
    {
        return $this->statusCommande;
    }

    public function setStatusCommande($value)
    {
        $this->statusCommande = $value;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
}
