<?php

namespace App\Controller;

use App\Entity\Match1;
use App\Entity\Listinscri;
use App\Form\Match1Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MatchController extends AbstractController
{
    #[Route('/organiser-match', name: 'app_organiser_match')]
    #[IsGranted('ROLE_USER')]
    public function organiserMatch(Request $request, EntityManagerInterface $entityManager): Response
    {
        $match = new Match1();
        $form = $this->createForm(Match1Type::class, $match);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $match->setId_user($this->getUser());
            $match->setStatut('en attente');
            
            try {
                $entityManager->persist($match);
                $entityManager->flush();
                
                $this->addFlash('success', 'Votre match a été créé avec succès!'); // <-- ICI
                return $this->redirectToRoute('app_organiser_match');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la création du match: '.$e->getMessage());
            }
        }

        $matchsEnAttente = $entityManager->getRepository(Match1::class)->findBy(
            ['statut' => 'en attente'],
            ['date' => 'ASC']
        );
        
        $sportsDisponibles = $this->getUniqueSports($matchsEnAttente);
    
        return $this->render('match/organiser_match.html.twig', [
            'form' => $form->createView(),
            'matchsEnAttente' => $matchsEnAttente,
            'sportsDisponibles' => $sportsDisponibles
        ]);
    }

    #[Route('/rejoindre-match', name: 'app_rejoindre_match')]
    #[IsGranted('ROLE_USER')]
    public function rejoindreMatch(EntityManagerInterface $entityManager): Response
    {
        $matchsDisponibles = $entityManager->getRepository(Match1::class)->findBy(
            ['statut' => ['en attente', 'confirmé']], // Notez les crochets pour le tableau
            ['date' => 'ASC']
        );
        
        $sportsDisponibles = $this->getUniqueSports($matchsDisponibles);
    
        return $this->render('match/rejoindre_match.html.twig', [
            'matchsEnAttente' => $matchsDisponibles,  // J'ai renommé la variable pour plus de cohérence
            'sportsDisponibles' => $sportsDisponibles
        ]);
    }
    #[Route('/participer-match/{id}', name: 'app_participate_match', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function participateMatch(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $match = $entityManager->getRepository(Match1::class)->find($id);
        
        if (!$match) {
            throw $this->createNotFoundException('Match non trouvé');
        }
    
        // Vérification CSRF
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('participate_match_'.$id, $submittedToken)) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_rejoindre_match');
        }
    
        if ($match->getStatut() !== 'en attente') {
            $this->addFlash('error', 'Ce match n\'est plus disponible pour rejoindre.');
            return $this->redirectToRoute('app_historique');
        }
    
        // Vérifier si l'utilisateur est l'organisateur
        if ($match->getId_user() === $this->getUser()) {
            $this->addFlash('warning', 'Vous ne pouvez pas rejoindre votre propre match.');
            return $this->redirectToRoute('app_historique');
        }
    
        // Vérifier si l'utilisateur est déjà inscrit
        $existingParticipation = $entityManager->getRepository(Listinscri::class)->findOneBy([
            'id_user' => $this->getUser(),
            'matchId' => $match
        ]);
    
        if ($existingParticipation) {
            $this->addFlash('warning', 'Vous participez déjà à ce match.');
            return $this->redirectToRoute('app_historique');
        }
    
        try {
            // Vérifier le nombre de participants actuels
            $participationsCount = $entityManager->getRepository(Listinscri::class)
                ->count(['matchId' => $match]);
            
            // Calcul du nombre maximum de participants autorisés (nbPersonne - 1)
            $maxParticipants = $match->getNb_personne() - 1;
            
            if ($participationsCount >= $maxParticipants) {
                $this->addFlash('error', 'Ce match a déjà atteint le nombre maximum de participants.');
                return $this->redirectToRoute('app_rejoindre_match');
            }
    
            // Créer une nouvelle inscription
            $participation = new Listinscri();
            $participation->setId_user($this->getUser());
            $participation->setMatchId($match);
         
            $entityManager->persist($participation);
            
            // Vérifier si le match est maintenant complet
            if (($participationsCount + 1) >= $maxParticipants) {
                $match->setStatut('confirmé');
                $this->addFlash('info', 'Le match est maintenant confirmé!');
            }
    
            $entityManager->flush();
            $this->addFlash('success', 'Vous avez rejoint le match avec succès!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue: '.$e->getMessage());
            error_log('Erreur participation match: ' . $e->getMessage());
        }
    
        return $this->redirectToRoute('app_historique');
    }
    #[Route('/historique', name: 'app_historique')]
    #[IsGranted('ROLE_USER')]
    public function historique(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // 1. Matchs organisés par l'utilisateur
        $matchsOrganises = $entityManager->getRepository(Match1::class)->findBy(
            ['id_user' => $user],
            ['date' => 'DESC']
        );

        // 2. Matchs où l'utilisateur est participant (mais pas organisateur)
        $participations = $entityManager->getRepository(Listinscri::class)->findBy(
            ['id_user' => $user],
            ['id' => 'DESC']
        );

        // Préparer les données pour le template
        $matchsParticipations = [];
        foreach ($participations as $participation) {
            $match = $participation->getMatchId();
            // Vérifier que l'utilisateur n'est pas l'organisateur
            if ($match->getId_user() !== $user) {
                $matchsParticipations[] = [
                    'match' => $match,
                    'participation_id' => $participation->getId()
                ];
            }
        }

        return $this->render('match/historique_match.html.twig', [
            'matchsOrganises' => $matchsOrganises,
            'matchsParticipations' => $matchsParticipations,
            'user' => $user
        ]);
    }

    #[Route('/match/{id}/edit', name: 'match_edit')]
#[IsGranted('ROLE_USER')]
public function edit(Request $request, Match1 $match, EntityManagerInterface $entityManager): Response
{
    if ($match->getId_user() !== $this->getUser()) {
        throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce match.');
    }

    $form = $this->createForm(Match1Type::class, $match, [
        'validation_groups' => ['Default', 'edit']
    ]);
    
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
        if ($form->isValid()) {
            // Vérifier si le nombre de participants actuels ne dépasse pas le nouveau nb_personne
            $currentParticipants = $match->getListinscris()->count();
            if ($currentParticipants > $match->getNb_personne() - 1) {
                $this->addFlash('error', sprintf(
                    'Impossible de réduire à %d places car il y a déjà %d participants.',
                    $match->getNb_personne(),
                    $currentParticipants
                ));
                return $this->redirectToRoute('match_edit', ['id' => $match->getId()]);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Match modifié avec succès');
            return $this->redirectToRoute('app_historique');
        } else {
            $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire');
        }
    }

    return $this->render('match/edit.html.twig', [
        'form' => $form->createView(),
        'match' => $match,
    ]);
}

    #[Route('/match/{id}/delete', name: 'match_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Match1 $match, EntityManagerInterface $entityManager): Response
    {
        if ($match->getId_user() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas annuler ce match.');
        }

        if ($this->isCsrfTokenValid('delete'.$match->getId(), $request->request->get('_token'))) {
            $entityManager->remove($match);
            $entityManager->flush();
            $this->addFlash('success', 'Match annulé avec succès');
        }

        return $this->redirectToRoute('app_historique');
    }

    #[Route('/participation/{id}/delete', name: 'participation_delete', methods: ['POST'])]
#[IsGranted('ROLE_USER')]
public function deleteParticipation(Request $request, Listinscri $participation, EntityManagerInterface $entityManager): Response
{
    if ($participation->getId_user() !== $this->getUser()) {
        throw $this->createAccessDeniedException('Vous ne pouvez pas quitter ce match.');
    }

    if ($this->isCsrfTokenValid('delete'.$participation->getId(), $request->request->get('_token'))) {
        $match = $participation->getMatchId();
        
        $entityManager->remove($participation);
        
        // Vérifier si le statut était confirmé ou complet
        if (in_array($match->getStatut(), ['confirmé', 'complet'])) {
            $match->setStatut('en attente');
            $this->addFlash('info', 'Le statut du match est revenu à "en attente"');
        }
        
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez quitté le match avec succès');
    }

    return $this->redirectToRoute('app_historique');
}

    private function getUniqueSports(array $matchs): array
    {
        $sports = [];
        foreach ($matchs as $match) {
            $sport = $match->getTypesport();
            if (!in_array($sport, $sports)) {
                $sports[] = $sport;
            }
        }
        sort($sports);
        return $sports;
    }
}