<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ForgotPasswordController extends AbstractController
{
    #[Route('/forgot-password', name: 'forgot_password')]
    public function forgotPassword(Request $request, EntityManagerInterface $em, MailerInterface $mailer)
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $em->getRepository(User::class)->findOneBy(['email_user' => $email]);
    
            if (!$user) {
                $this->addFlash('danger', 'Email non trouvé.');
                return $this->redirectToRoute('forgot_password');
            }
    
            // Générer un token
            $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->setResetToken($token);
            $em->flush();
    
            // Envoyer email
            $emailMessage = (new Email())
                ->from('hbibbensalem20@gmail.com')
                ->to($user->getEmailUser())
                ->subject('Réinitialisation de mot de passe')
                ->html('<p>Votre code est : <strong>'.$token.'</strong></p>');
    
            try {
                $mailer->send($emailMessage);
                $this->addFlash('success', 'Email envoyé ! Vérifiez votre boîte de réception.');
                
                // Correction ici : Utilisez le nom correct du paramètre
                return $this->redirectToRoute('verify_token', ['email' => $user->getEmailUser()]);
                
            } catch (\Exception $e) {
                $this->addFlash('danger', "Erreur d'envoi : ".$e->getMessage());
                return $this->redirectToRoute('forgot_password');
            }
        }
    
        return $this->render('forgot_password/forgot_password.html.twig');
    }

    #[Route('/verify-token', name: 'verify_token')]
    public function verifyToken(Request $request, EntityManagerInterface $em)
    {
        // Correction: utiliser 'email' partout
        $email = $request->query->get('email');
        $user = $em->getRepository(User::class)->findOneBy(['email_user' => $email]);
    
        if (!$user) {
            $this->addFlash('danger', 'Email non trouvé.');
            return $this->redirectToRoute('forgot_password');
        }
    
        if ($request->isMethod('POST')) {
            $token = $request->request->get('token');
            if ($user->getResetToken() === $token) {
                // Correction: utiliser 'email' au lieu de 'email_user'
                return $this->redirectToRoute('reset_password', ['email' => $email]);
            } else {
                $this->addFlash('danger', 'Token invalide.');
            }
        }
    
        return $this->render('forgot_password/verify_token.html.twig', ['email' => $email]);
    }
    
    #[Route('/reset-password', name: 'reset_password')]
    public function resetPassword(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        // Correction: utiliser 'email' partout
        $email = $request->query->get('email');
        $user = $em->getRepository(User::class)->findOneBy(['email_user' => $email]);
    
        if (!$user) {
            $this->addFlash('danger', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('forgot_password');
        }
    
        if ($request->isMethod('POST')) {
            // Correction: vérifier le bon nom de champ
            $newPassword = $request->request->get('password');
            
            if (empty($newPassword)) {
                $this->addFlash('danger', 'Le mot de passe ne peut pas être vide');
                return $this->render('forgot_password/reset_password.html.twig', ['email' => $email]);
            }
    
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            // Correction: utiliser la bonne méthode setter
            $user->setPasswordUser($hashedPassword);
            $user->setResetToken(null);
            $em->flush();
    
            $this->addFlash('success', 'Mot de passe réinitialisé avec succès.');
            return $this->redirectToRoute('app_login');
        }
    
        return $this->render('forgot_password/reset_password.html.twig', ['email' => $email]);
    }

    #[Route('/forgot-password/request', name: 'app_forgot_password_request')]
    public function resendToken(Request $request, EntityManagerInterface $em, MailerInterface $mailer): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $email = $request->query->get('email');
        $user = $em->getRepository(User::class)->findOneBy(['email_user' => $email]);

        if (!$user) {
            $this->addFlash('danger', 'Email non trouvé.');
            return $this->redirectToRoute('forgot_password');
        }

        // Générer un nouveau token
        $token = bin2hex(random_bytes(10));
        $user->setResetToken($token);
        $em->flush();

        // Envoyer email
        $emailMessage = (new Email())
            ->from('hbibbensalem20@gmail.com')
            ->to($user->getEmailUser())
            ->subject('Votre nouveau code de réinitialisation')
            ->text('Votre nouveau code de réinitialisation est : ' . $token);

        try {
            $mailer->send($emailMessage);
            $this->addFlash('success', 'Un nouveau code a été envoyé à votre email.');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
        }

        return $this->redirectToRoute('verify_token', ['email' => $email]);
    }
}
