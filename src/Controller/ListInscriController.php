<?php

namespace App\Controller;

use App\Entity\Listinscri;
use App\Entity\Match1;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ListInscriController extends AbstractController
{
    #[Route('/test-reservation/{id}', name: 'app_test_reservation', methods: ['POST'])]
    public function testReservation(int $id, EntityManagerInterface $entityManager): Response
    {
        // 1. Vérifier que l'utilisateur est connecté
        if (!$this->getUser()) {
            throw new AccessDeniedException('Vous devez être connecté pour effectuer cette action.');
        }

        // 2. Récupérer l'utilisateur connecté
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        // 3. Récupérer le match
        $match = $entityManager->getRepository(Match1::class)->find($id);
        
        if (!$match) {
            return new Response('Match non trouvé', 400);
        }
        
        // 4. Vérifier si l'utilisateur est déjà inscrit
        $existingInscription = $entityManager->getRepository(Listinscri::class)->findOneBy([
            'id_user' => $user,
            'matchId' => $match
        ]);
        
        if ($existingInscription) {
            return new Response('Vous êtes déjà inscrit à ce match', 400);
        }
        
        // 5. Créer l'inscription
        $inscription = new Listinscri();
        $inscription->setId_user($user);
        $inscription->setMatchId($match);
        
        // 6. Enregistrer
        $entityManager->persist($inscription);
        $entityManager->flush();
        
        // 7. Retourner une réponse détaillée
        return new Response(sprintf(
            'Inscription réussie!<br>'
            . 'Utilisateur: %s (ID: %d)<br>'
            . 'Match: %s (ID: %d)<br>'
            . 'Inscription ID: %d',
            $user->getEmailUser(),
            $user->getIdUser(),
            $match->getTypeSport(),
            $match->getId(),
            $inscription->getId()
        ));
    }
}