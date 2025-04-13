<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Produit;

#[ORM\Entity]
class Commande
{

    #[ORM\Id]
    #[ORM\Column(type: "string", length: 255)]
    private string $id_commande;

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "commandes")]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user', onDelete: 'CASCADE')]
    private User $id_user;

        #[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: "commandes")]
    #[ORM\JoinColumn(name: 'idProduit', referencedColumnName: 'id_produit', onDelete: 'CASCADE')]
    private Produit $idProduit;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date_commande;

    #[ORM\Column(type: "integer")]
    private int $quantite_commande;

    #[ORM\Column(type: "string", length: 255)]
    private string $status_commande;

    public function getId_commande()
    {
        return $this->id_commande;
    }

    public function setId_commande($value)
    {
        $this->id_commande = $value;
    }

    public function getId_user()
    {
        return $this->id_user;
    }

    public function setId_user($value)
    {
        $this->id_user = $value;
    }

    public function getIdProduit()
    {
        return $this->idProduit;
    }

    public function setIdProduit($value)
    {
        $this->idProduit = $value;
    }

    public function getDate_commande()
    {
        return $this->date_commande;
    }

    public function setDate_commande($value)
    {
        $this->date_commande = $value;
    }

    public function getQuantite_commande()
    {
        return $this->quantite_commande;
    }

    public function setQuantite_commande($value)
    {
        $this->quantite_commande = $value;
    }

    public function getStatus_commande()
    {
        return $this->status_commande;
    }

    public function setStatus_commande($value)
    {
        $this->status_commande = $value;
    }
}
