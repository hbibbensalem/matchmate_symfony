<?php
namespace App\Service;

use App\Entity\Commande;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;
use Twig\Environment;

class OrderMailer
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
        private Environment $twig
    ) {}

    public function sendConfirmation(Commande $commande): void
    {
        $user = $commande->getUser();
        
        try {
            $email = (new Email())
                ->from('nourhenkhaz888@gmail.com@gmail.com')
                ->to($user->getEmailUser())
                ->subject('Confirmation commande #'.$commande->getIdCommande())
                ->html($this->twig->render('emails/order_confirmation.html.twig', [
                    'commande' => $commande,
                    'user' => $user,
                    'produit' => $commande->getProduit()
                ]));

            $this->mailer->send($email);
            
        } catch (\Exception $e) {
            $this->logger->error('Erreur envoi email', [
                'error' => $e->getMessage(),
                'commande' => $commande->getIdCommande()
            ]);
            // Ne pas propager l'exception pour ne pas interrompre le flux
        }
    }
}