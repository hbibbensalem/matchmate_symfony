<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Form\ProfileType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class AuthController extends AbstractController
{
    #[Route('/auth', name: 'app_auth')]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);



        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $role = $form->get('role')->getData();
        
                // Gérer les champs selon le rôle
                if ($role === 'PLAYER') {
                    $user->setExperience(null);
                    $user->setSalaire(null);
                } elseif ($role === 'NUTRITIONIST') {
                    $user->setNiveauJoueur(null);
                    $user->setIsPremium(null);
                }
                try {
                    // Debugging: Log form data
                    dump($user);

                    // Ensure generateCustomId is called
                    if (method_exists($user, 'generateCustomId')) {
                        $user->generateCustomId();
                    } else {
                        throw new \Exception('generateCustomId method is missing in User entity.');
                    }

                    // Handle photo upload
                    $photoFile = $form->get('photo_user')->getData();
                    if ($photoFile) {
                        $newFilename = uniqid().'.'.$photoFile->guessExtension();
                        $photoFile->move(
                            $this->getParameter('uploads_directory'),
                            $newFilename
                        );
                        $user->setPhotoUser($newFilename);
                    }

                    // Handle file upload
                    $pieceJointeFile = $form->get('piece_jointe')->getData();
                    if ($pieceJointeFile) {
                        $newFilename = uniqid().'.'.$pieceJointeFile->guessExtension();
                        $pieceJointeFile->move(
                            $this->getParameter('uploads_directory'),
                            $newFilename
                        );
                        $user->setPieceJointe($newFilename);
                    }

                    // Hash password
                    $hashedPassword = $passwordHasher->hashPassword(
                        $user,
                        $form->get('password_user')->getData()
                    );
                    $user->setPasswordUser($hashedPassword);

                    // Set default values
                    $user->setIsActive(true);
                    $user->setIsPremium('0');

                    // Persist user
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
                    return $this->redirectToRoute('app_login');

                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue: '.$e->getMessage());
                    dump($e->getMessage()); // Debugging: Log the exception
                }
            } else {
                // Log validation errors
                $errors = [];
                foreach ($form->getErrors(true) as $error) {
                    $errors[] = $error->getMessage();
                }
                $this->addFlash('error', 'Le formulaire contient des erreurs: ' . implode(', ', $errors));
                dump($errors); // Debugging: Log validation errors
            }
        } else {
            $this->addFlash('error', 'Le formulaire n\'a pas été soumis correctement.');
            dump('Form not submitted'); // Debugging: Log form submission status
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/login', name: 'app_login')]
public function login(AuthenticationUtils $authenticationUtils): Response
{
    $user = $this->getUser();

    if ($user instanceof User) {
        // Debug: Check the actual role value
        dump($user->getRole()); // Should output "ADMIN", "PLAYER", etc.

        // Check if the role is 'ADMIN' (string comparison)
        if ($user->getRole() === 'ADMIN') {
            return $this->redirectToRoute('app_home_back');
        }
        return $this->redirectToRoute('app_home_front');
    }

    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('auth/login.html.twig', [
        'last_username' => $lastUsername,
        'error' => $error,
    ]);
}

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony will intercept this route and handle the logout process
    }

    #[Route('/auth/editProfile', name: 'edit_profile')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Vous devez être connecté');
        }

        // Sauvegarde de l'ancienne photo
        $oldPhoto = $user->getPhotoUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Gestion du mot de passe
                if ($form->get('password_user')->getData()) {
                    $user->setPasswordUser(
                        $passwordHasher->hashPassword(
                            $user,
                            $form->get('password_user')->getData()
                        )
                    );
                }

                // Gestion de la photo
                $photoFile = $form->get('photo_user')->getData();
                if ($photoFile) {
                    $newFilename = uniqid().'.'.$photoFile->guessExtension();
                    $photoFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    $user->setPhotoUser($newFilename);
                    
                    // Suppression de l'ancienne photo
                    if ($oldPhoto && file_exists($this->getParameter('uploads_directory').'/'.$oldPhoto)) {
                        unlink($this->getParameter('uploads_directory').'/'.$oldPhoto);
                    }
                }

                $entityManager->flush();
                $this->addFlash('success', 'Profil mis à jour avec succès!');
                return $this->redirectToRoute('edit_profile');

            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la mise à jour: '.$e->getMessage());
            }
        }

        return $this->render('auth/editProfile.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}