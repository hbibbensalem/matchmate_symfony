<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Match1;
use App\Repository\Match1Repository;
use App\Repository\ListinscriRepository;

class DashboardMatchController extends AbstractController
{
    private $matchRepository;
    private $listinscriRepository;

    public function __construct(Match1Repository $matchRepository, ListinscriRepository $listinscriRepository)
    {
        $this->matchRepository = $matchRepository;
        $this->listinscriRepository = $listinscriRepository;
    }

    #[Route("/dashboard/matchs", name: "match_dashboard")]
    public function index(): Response
    {
        // Récupérer tous les matchs triés par date décroissante
        $matchs = $this->matchRepository->findBy([], ['date' => 'DESC', 'heure' => 'DESC']);

        return $this->render('match/matchBack.html.twig', [
            'matchs' => $matchs,
        ]);
    }

    #[Route("/dashboard/match/{matchId}/participants", name: "match_participants", methods: ["GET"])]
    public function getParticipants(int $matchId): JsonResponse
    {
        $match = $this->matchRepository->find($matchId);
        
        if (!$match) {
            return new JsonResponse([
                'error' => 'Match not found'
            ], Response::HTTP_NOT_FOUND);
        }
    
        $participants = $this->listinscriRepository->findBy(['matchId' => $match]);
    
        $response = [];
        foreach ($participants as $participant) {
            $user = $participant->getId_user(); // Changed from getIdUser()
            if ($user) {
                $response[] = [
                    'id_user' => $user->getIdUser(),
                    'nom_user' => $user->getNomUser(),
                    'prenom_user' => $user->getPrenomUser(),
                    'email_user' => $user->getEmailUser(),
                    'telephone_user' => $user->getTelephoneUser() ?? 'N/A',
                    // Remove niveau_joueur since it's not in your entity
                ];
            }
        }
    
        return new JsonResponse($response);
    }
}