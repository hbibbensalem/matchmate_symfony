<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Suivi;


#[ORM\Entity]
class User implements UserInterface, PasswordAuthenticatedUserInterface 
{

    #[ORM\Id]
    #[ORM\Column(type: "string", length: 255 , unique: true)]
    private ?string $id_user= null;


    public function getRoles(): array
    {
        return $this->roles ?? ['ROLE_USER']; // Ensure a default role is provided
    }

    public function eraseCredentials(): void
    {
        // Clear any sensitive data if stored temporarily
    }

    public function getUserIdentifier(): string
    {
        return $this->email_user; // Corrected property name
    }

    public function getPassword(): ?string
    {
        return $this->password_user; // Corrected property name
    }

    public function __toString(): string
{
    return sprintf('%s %s (%s)', 
        $this->getNomUser(), 
        $this->getPrenomUser(), 
        $this->getEmailUser()
    );
}

public function __construct()
{
    $this->generateCustomId();
}

public function generateCustomId(): void
{
    // Use the role that was set, default to 'PLAYER' if not set
    $role = $this->role ?? 'PLAYER';
    $middle = '';
    
    switch ($role) {
        case 'PLAYER':
            $middle = ($this->sexe_user === 'M') ? 'JMT' : 'FMT';
            break;
        case 'NUTRITIONIST':
            $middle = 'NUT';
            break;
        case 'ADMIN':
            $middle = 'ADM';
            break;
        default:
            $middle = 'GEN';
    }
    
    $randomPrefix = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
    $randomSuffix = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    
    $this->id_user = $randomPrefix . $middle . $randomSuffix;
}



    #[ORM\Column(type: "string", length: 255)]
    private ?string $email_user;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $password_user;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $nom_user;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $prenom_user;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $sexe_user = 'M'; // Default value added

    #[ORM\Column(type: "string", length: 15)]
    private ?string $telephone_user;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $adresse_user;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $role;

    #[ORM\Column(type: "string", length: 255 , nullable: true)]
    private ?string $experience;

    #[ORM\Column(type: "float" , nullable: true)]
    private ?float $salaire;

    #[ORM\Column(type: "string", length: 255 , nullable: true)]
    private ?string $niveau_joueur;

    #[ORM\Column(type: "integer")]
    private ?int $max_distance_user;

    #[ORM\Column(type: "string", length: 1 , nullable: true)]
    private ?string $is_premium;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $photo_user;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $piece_jointe;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $reset_token;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $token_expiration;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $date_naissance_user;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $reactivateAt = null;
    

    #[ORM\Column(type: "string", length: 255)]
    private ?string $taille;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $poids;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $tour_de_taille;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $maladie;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $description_user = null; 
    
    #[ORM\Column(type: 'boolean')]
    private bool $isTwoFactorEnabled = false; // Added property

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $stripeCustomerId = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $face_image = null;

    #[ORM\Column(type: 'float', nullable: true)]
private ?float $latitude = null;

#[ORM\Column(type: 'float', nullable: true)]
private ?float $longitude = null;

    public function getDescriptionUser(): ?string
    {
        return $this->description_user;
    }

    public function setDescriptionUser(?string $value): self
    {
        $this->description_user = $value;
        return $this;
    }

    public function getIdUser()
    {
        return $this->id_user;
    }

    public function setIdUser($value)
    {
        $this->id_user = $value;
    }

    public function getEmailUser(): ?string
    {
        return $this->email_user;
    }

    public function setEmailUser($value)
    {
        $this->email_user = $value;
    }

    public function getPasswordUser()
    {
        return $this->password_user;
    }

    public function setPasswordUser($value)
    {
        $this->password_user = $value;
    }

    public function getNomUser()
    {
        return $this->nom_user;
    }

    public function setNomUser($value)
    {
        $this->nom_user = $value;
    }

    public function getPrenomUser()
    {
        return $this->prenom_user;
    }

    public function setPrenomUser($value)
    {
        $this->prenom_user = $value;
    }

    public function getSexeUser()
    {
        return $this->sexe_user;
    }

    public function setSexeUser($value)
    {
        $this->sexe_user = $value;
    }

    public function getTelephoneUser()
    {
        return $this->telephone_user;
    }

    public function setTelephoneUser($value)
    {
        $this->telephone_user = $value;
    }

    public function getAdresseUser()
    {
        return $this->adresse_user;
    }

    public function setAdresseUser($value)
    {
        $this->adresse_user = $value;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole($value)
    {
        $this->role = $value;
    }

    public function getExperience()
    {
        return $this->experience;
    }

    public function setExperience($value)
    {
        $this->experience = $value;
    }

    public function getSalaire()
    {
        return $this->salaire;
    }

    public function setSalaire($value)
    {
        $this->salaire = $value;
    }

    public function getNiveauJoueur()
    {
        return $this->niveau_joueur;
    }

    public function setNiveauJoueur($value)
    {
        $this->niveau_joueur = $value;
    }

    public function getMaxDistanceUser()
    {
        return $this->max_distance_user;
    }

    public function setMaxDistanceUser($value)
    {
        $this->max_distance_user = $value;
    }

    public function getIsPremium()
    {
        return $this->is_premium;
    }

    public function setIsPremium($value)
    {
        $this->is_premium = $value;
    }

    public function getPhotoUser()
    {
        return $this->photo_user;
    }

    public function setPhotoUser($value)
    {
        $this->photo_user = $value;
    }

    public function getPieceJointe()
    {
        return $this->piece_jointe;
    }

    public function setPieceJointe($value)
    {
        $this->piece_jointe = $value;
    }

    public function getResetToken()
    {
        return $this->reset_token;
    }

    public function setResetToken($value)
    {
        $this->reset_token = $value;
    }

    public function getTokenExpiration()
    {
        return $this->token_expiration;
    }

    public function setTokenExpiration($value)
    {
        $this->token_expiration = $value;
    }

    public function getDateNaissanceUser()
    {
        return $this->date_naissance_user;
    }

    public function setDateNaissanceUser($value)
    {
        $this->date_naissance_user = $value;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getReactivateAt(): ?\DateTimeInterface
    {
        return $this->reactivateAt;
    }
    public function setReactivateAt(?\DateTimeInterface $reactivateAt): self
    {
        $this->reactivateAt = $reactivateAt;
        return $this;
    }

    public function getTaille()
    {
        return $this->taille;
    }

    public function setTaille($value)
    {
        $this->taille = $value;
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

    public function getMaladie()
    {
        return $this->maladie;
    }

    public function setMaladie($value)
    {
        $this->maladie = $value;
    }
    public function isTwoFactorEnabled(): bool
{
    return $this->isTwoFactorEnabled;
}

public function getStripeCustomerId(): ?string
    {
        return $this->stripeCustomerId;
    }

    public function setStripeCustomerId(?string $stripeCustomerId): self
    {
        $this->stripeCustomerId = $stripeCustomerId;
        return $this;
    }

public function setIsTwoFactorEnabled(bool $isTwoFactorEnabled): self
{
    $this->isTwoFactorEnabled = $isTwoFactorEnabled;
    return $this;
}

public function getFaceImage(): ?string
{
    return $this->face_image;
}

public function setFaceImage(?string $face_image): self
{
    $this->face_image = $face_image;
    return $this;
}
public function getLatitude(): ?float
{
    return $this->latitude;
}
public function setLatitude(?float $latitude): self
{
    $this->latitude = $latitude;
    return $this;
}
public function getLongitude(): ?float
{
    return $this->longitude;
}
public function setLongitude(?float $longitude): self
{
    $this->longitude = $longitude;
    return $this;

}


    #[ORM\OneToMany(mappedBy: "id_user", targetEntity: Event::class)]
    private Collection $events;

        public function getEvents(): Collection
        {
            return $this->events;
        }
    
        public function addEvent(Event $event): self
        {
            if (!$this->events->contains($event)) {
                $this->events[] = $event;
                $event->setId_user($this);
            }
    
            return $this;
        }
    
        public function removeEvent(Event $event): self
        {
            if ($this->events->removeElement($event)) {
                // set the owning side to null (unless already changed)
                if ($event->getId_user() === $this) {
                    $event->setId_user(null);
                }
            }
    
            return $this;
        }

    #[ORM\OneToMany(mappedBy: "id_user", targetEntity: Match1::class)]
    private Collection $match1s;

    #[ORM\OneToMany(mappedBy: "id_user", targetEntity: Regime::class)]
    private Collection $regimes;

    #[ORM\OneToMany(mappedBy: "id_user", targetEntity: Commande::class)]
    private Collection $commandes;

    #[ORM\OneToMany(mappedBy: "id_user", targetEntity: Listinscri::class)]
    private Collection $listinscris;

    #[ORM\OneToMany(mappedBy: "id_user", targetEntity: Participation::class)]
    private Collection $participations;

    #[ORM\OneToMany(mappedBy: "id_user", targetEntity: Suivi::class)]
    private Collection $suivis;
}
