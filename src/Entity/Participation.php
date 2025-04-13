<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Event;

#[ORM\Entity]
class Participation
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $idparticipation;

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "participations")]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user', onDelete: 'CASCADE')]
    private User $id_user;

        #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: "participations")]
    #[ORM\JoinColumn(name: 'idevent', referencedColumnName: 'idevent', onDelete: 'CASCADE')]
    private Event $idevent;

    public function getIdparticipation()
    {
        return $this->idparticipation;
    }

    public function setIdparticipation($value)
    {
        $this->idparticipation = $value;
    }

    public function getId_user()
    {
        return $this->id_user;
    }

    public function setId_user($value)
    {
        $this->id_user = $value;
    }

    public function getIdevent()
    {
        return $this->idevent;
    }

    public function setIdevent($value)
    {
        $this->idevent = $value;
    }
}
