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
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Form\ProfileType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\FaceService;
use App\Service\FileUploader;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


final class AuthController extends AbstractController
{
    #[Route('/auth', name: 'app_auth')]
public function register(
    Request $request,
    EntityManagerInterface $entityManager,
    UserPasswordHasherInterface $passwordHasher,
    UserRepository $userRepository,
    FileUploader $fileUploader
): Response {
    $user = new User();
    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
        if ($form->isValid()) {
            $role = $form->get('role')->getData();
            $isNutritionist = ($role === 'NUTRITIONIST');
            $diplomeValide = false;

            // Traitement spécifique pour les nutritionnistes
            if ($isNutritionist) {
                $pieceJointeFile = $form->get('piece_jointe')->getData();
                
                if ($pieceJointeFile) {
                    $newFilename = md5(uniqid().$pieceJointeFile->getClientOriginalName()).'.'.$pieceJointeFile->guessExtension();
                    $destination = $this->getParameter('uploads_directory');
                    $fullPath = $destination.'/'.$newFilename;

                    // Déplacer le fichier
                    $pieceJointeFile->move($destination, $newFilename);

                    if (!file_exists($fullPath)) {
                        $this->addFlash('error', 'Erreur lors du téléchargement du fichier.');
                        return $this->redirectToRoute('app_auth');
                    }

                    // Analyse OCR
                    $text = (new TesseractOCR($fullPath))
                        ->lang('fra')
                        ->psm(6)
                        ->run();
                    $text = strtolower(trim($text));

                    // Vérification des mots-clés
                    $motsCles = ['nutrition', 'nutritherapie', 'nutrithérapie'];
                    $trouve = false;
                    foreach ($motsCles as $mot) {
                        if (str_contains($text, $mot)) {
                            $trouve = true;
                            break;
                        }
                    }

                    if ($trouve) {
                        $diplomeValide = true;
                        $user->setPieceJointe($newFilename);
                    } else {
                        unlink($fullPath);
                        $this->addFlash('error', 'Diplôme non reconnu. ');
                        return $this->redirectToRoute('app_auth');
                    }
                } else {
                    $this->addFlash('error', 'Un diplôme est requis pour les nutritionnistes.');
                    return $this->redirectToRoute('app_auth');
                }
            }

            // Vérification email existant
            $existingUser = $userRepository->findOneBy(['email_user' => $user->getEmailUser()]);
            if ($existingUser) {
                $this->addFlash('error', 'Cet email est déjà utilisé.');
                return $this->render('auth/register.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            // Gestion des champs selon le rôle
            if ($role === 'PLAYER') {
                $user->setExperience(null);
                $user->setSalaire(null);
            } elseif ($role === 'NUTRITIONIST') {
                $user->setNiveauJoueur(null);
                $user->setIsPremium(null);
            }

            try {
                // Génération de l'ID custom
                if (method_exists($user, 'generateCustomId')) {
                    $user->generateCustomId();
                }

                // Traitement de la photo webcam
                $faceImageData = $request->request->get('face_image_data');
                if ($faceImageData) {
                    $faceImageName = $fileUploader->uploadBase64Image($faceImageData);
                    $user->setFaceImage($faceImageName);
                }

                // Traitement photo utilisateur
                $photoFile = $form->get('photo_user')->getData();
                if ($photoFile) {
                    $newFilename = md5(uniqid().$photoFile->getClientOriginalName()).'.'.$photoFile->guessExtension();
                    $photoFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    $user->setPhotoUser($newFilename);
                }

                // Hash password
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $form->get('password_user')->getData()
                );
                $user->setPasswordUser($hashedPassword);

                // Définition du statut actif
                $user->setIsActive(!$isNutritionist); // true pour players, false pour nutritionnistes
                $user->setIsPremium(false);

                // Persist user
                $entityManager->persist($user);
                $entityManager->flush();

                // Message de succès différent selon le rôle
                $this->addFlash(
                    $isNutritionist ? 'warning' : 'success',
                    $isNutritionist 
                        ? 'Compte créé. En attente de validation par l\'administrateur.' 
                        : 'Inscription réussie ! Vous pouvez maintenant vous connecter.'
                );

                // Redirection vers la page de connexion
                return $this->redirectToRoute('app_login');

            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue: '.$e->getMessage());
                // Nettoyage des fichiers uploadés si erreur
                if (isset($newFilename) && file_exists($destination.'/'.$newFilename)) {
                    unlink($destination.'/'.$newFilename);
                }
            }
        } else {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }
            $this->addFlash('error', 'Le formulaire contient des erreurs: '.implode(', ', $errors));
        }
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
        if (!$user->isActive()) {
            $this->container->get('security.token_storage')->setToken(null);
            
            $reactivateAt = $user->getReactivateAt();
            $errorMessage = 'Votre compte est désactivé ';
            
            if ($reactivateAt instanceof \DateTimeInterface) {
                // Format simple sans intl
                $formattedDate = $reactivateAt->format('d/m/Y à H:i');
                $errorMessage .= "jusqu'au ".$formattedDate;
            } else {
                $errorMessage .= "définitivement";
            }
            
            $this->addFlash('error', $errorMessage);
            return $this->redirectToRoute('app_login');
        }

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