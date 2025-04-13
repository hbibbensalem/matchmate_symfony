<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Support;

#[ORM\Entity]
class Reclamation
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_r;

        #[ORM\ManyToOne(targetEntity: Support::class, inversedBy: "reclamations")]
    #[ORM\JoinColumn(name: 'id_s', referencedColumnName: 'id_s', onDelete: 'CASCADE')]
    private Support $id_s;

    #[ORM\Column(type: "string", length: 255)]
    private string $contenu;

    #[ORM\Column(type: "string", length: 255)]
    private string $categorie;

    #[ORM\Column(type: "string", length: 255)]
    private string $support;

    public function getId_r()
    {
        return $this->id_r;
    }

    public function setId_r($value)
    {
        $this->id_r = $value;
    }

    public function getId_s()
    {
        return $this->id_s;
    }

    public function setId_s($value)
    {
        $this->id_s = $value;
    }

    public function getContenu()
    {
        return $this->contenu;
    }

    public function setContenu($value)
    {
        $this->contenu = $value;
    }

    public function getCategorie()
    {
        return $this->categorie;
    }

    public function setCategorie($value)
    {
        $this->categorie = $value;
    }

    public function getSupport()
    {
        return $this->support;
    }

    public function setSupport($value)
    {
        $this->support = $value;
    }
}
