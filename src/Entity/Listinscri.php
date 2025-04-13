<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Match1;

#[ORM\Entity]
class Listinscri
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "listinscris")]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user', onDelete: 'CASCADE')]
    private User $id_user;

        #[ORM\ManyToOne(targetEntity: Match1::class, inversedBy: "listinscris")]
    #[ORM\JoinColumn(name: 'matchId', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Match1 $matchId;

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

    public function getMatchId()
    {
        return $this->matchId;
    }

    public function setMatchId($value)
    {
        $this->matchId = $value;
    }
}
