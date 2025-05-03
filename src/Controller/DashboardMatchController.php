<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Match1;
use App\Repository\Match1Repository;
use App\Repository\ListinscriRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
    #[Route("/dashboard/matchs/export-pdf", name: "match_export_pdf")]
public function exportToPdf(): Response
{
    // Récupérer tous les matchs triés par date
    $matchs = $this->matchRepository->findBy([], ['date' => 'DESC', 'heure' => 'DESC']);

    // Générer le HTML pour le PDF
    $html = $this->renderView('match/export_pdf.html.twig', [
        'matchs' => $matchs,
        'title' => 'Liste des Matchs'
    ]);

    // Configurer DomPDF
    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    // Générer le nom du fichier
    $filename = sprintf('liste-matchs-%s.pdf', date('Y-m-d'));

    // Retourner la réponse PDF
    return new Response(
        $dompdf->output(),
        Response::HTTP_OK,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename)
        ]
    );
}
#[Route("/dashboard/matchs/export-excel", name: "match_export_excel")]
public function exportToExcel(): Response
{
    $matchs = $this->matchRepository->findBy([], ['date' => 'DESC', 'heure' => 'DESC']);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // En-têtes
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'Organisateur');
    $sheet->setCellValue('C1', 'Email');
    $sheet->setCellValue('D1', 'Date');
    $sheet->setCellValue('E1', 'Heure');
    $sheet->setCellValue('F1', 'Localisation');
    $sheet->setCellValue('G1', 'Terrain');
    $sheet->setCellValue('H1', 'Sport');
    $sheet->setCellValue('I1', 'Participants');
    $sheet->setCellValue('J1', 'Statut');

    // Données
    $row = 2;
    foreach ($matchs as $match) {
        $user = $match->getId_user();
        
        $sheet->setCellValue('A'.$row, $match->getId());
        $sheet->setCellValue('B'.$row, $user->getNomUser().' '.$user->getPrenomUser());
        $sheet->setCellValue('C'.$row, $user->getEmailUser());
        $sheet->setCellValue('D'.$row, $this->formatDate($match->getDate()));
        $sheet->setCellValue('E'.$row, $this->formatTime($match->getHeure()));
        $sheet->setCellValue('F'.$row, $match->getLocalisation());
        $sheet->setCellValue('G'.$row, $match->getTerrain());
        $sheet->setCellValue('H'.$row, $match->getTypesport());
        $sheet->setCellValue('I'.$row, count($match->getListinscris()).'/'.$match->getNb_personne());
        $sheet->setCellValue('J'.$row, $match->getStatut());
        
        $row++;
    }

    // Formatage automatique des colonnes
    foreach(range('A','J') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Création du fichier
    $writer = new Xlsx($spreadsheet);
    
    // Stockage temporaire
    $tempFile = tempnam(sys_get_temp_dir(), 'matchs');
    $writer->save($tempFile);

    // Envoi de la réponse
    $response = new Response(
        file_get_contents($tempFile),
        Response::HTTP_OK,
        [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => sprintf(
                'attachment; filename="liste-matchs-%s.xlsx"', 
                date('Y-m-d')
    )]
    );

    // Suppression du fichier temporaire
    unlink($tempFile);

    return $response;
}

private function formatDate($date): string
{
    return $date instanceof \DateTimeInterface 
        ? $date->format('d/m/Y') 
        : (string) $date;
}

private function formatTime($time): string
{
    return $time instanceof \DateTimeInterface 
        ? $time->format('H:i') 
        : (string) $time;
} 
#[Route("/dashboard/matchs/stats", name: "match_stats")]
public function stats(Match1Repository $matchRepository): Response
{
    $matchByMonth = $matchRepository->getMatchCountByMonth();
    
    return $this->render('match/stats.html.twig', [
        'sportsByLocation' => $matchRepository->getSportsCountByLocation(),
        'popularSports' => $matchRepository->getMostPopularSports(),
        'matchStatus' => $matchRepository->getMatchCountByStatus(),
        'matchByMonth' => $matchByMonth,
        'participantsBySport' => $matchRepository->getParticipantCountBySport(),
    ]);
}
}