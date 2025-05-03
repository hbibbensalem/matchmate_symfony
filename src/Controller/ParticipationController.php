<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\User;
use App\Entity\Event;
use Endroid\QrCode\QrCode;
use Psr\Log\LoggerInterface;
use App\Entity\Participation;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Endroid\QrCode\Color\Color;



use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Date;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use PhpOffice\PhpSpreadsheet\Style\{Alignment, Border,  Fill};

class ParticipationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger
    ) {}

    #[Route('/event/{id}/participants', name: 'app_event_participants')]
    public function showParticipants(Event $event): Response
    {
        $participations = $this->em->getRepository(Participation::class)
            ->findBy(['idevent' => $event]);
    
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
    
        return $this->render('participation/listeParticipants.html.twig', [
            'event' => $event,
            'participations' => $participations,
            'user' => $user // <- Ajout de l'utilisateur au template
        ]);
    }
    

    #[Route('/user/{id}/participations', name: 'app_user_participations')]
    public function getUserParticipations(User $user): Response
    {
        $participations = $this->em->getRepository(Participation::class)
            ->findBy(['id_user' => $user]); // nom correct de la propriété

        return $this->render('participation/historique_participantback.html.twig', [
            'participations' => $participations,
            'user' => $user
        ]);
    }

  
    #[Route('/participation/{id_event}', name: 'app_event_participation', requirements: ['id_event' => '\d+'], methods: ['POST'])]
#[IsGranted('ROLE_USER')]
public function participateEvent(int $id_event, Request $request): Response
{
    $event = $this->em->getRepository(Event::class)->find($id_event);
    $currentUser = $this->getUser();

    // Vérification de l'existence de l'événement
    if (!$event) {
        $this->addFlash('error', 'Événement non trouvé.');
        return $this->redirectToRoute('app_events');
    }

    // Vérification CSRF
    $submittedToken = $request->request->get('_token');
    if (!$this->isCsrfTokenValid('participate' . $id_event, $submittedToken)) {
        $this->addFlash('error', 'Token de sécurité invalide.');
        return $this->redirectToRoute('app_event_details', ['id' => $id_event]);
    }

    // Vérifier si l'utilisateur est déjà inscrit
    $existingParticipation = $this->em->getRepository(Participation::class)->findOneBy([
        'id_user' => $currentUser,
        'idevent' => $event
    ]);

    if ($existingParticipation) {
        $this->addFlash('warning', 'Vous êtes déjà inscrit à cet événement.');
        return $this->redirectToRoute('app_event_details', ['id' => $id_event]);
    }

    // Vérifier la capacité
    if ($event->isFull()) {
        $this->addFlash('warning', 'Cet événement est complet.');
        return $this->redirectToRoute('app_event_details', ['id' => $id_event]);
    }

    try {
        // Création de la participation
        $participation = new Participation();
        $participation->setId_user($currentUser);
        $participation->setIdevent($event);

        $this->em->persist($participation);
        $this->em->flush();

        // Génération du QR Code
        $qrData = json_encode([
            'event_title' => $event->getTitre(),
            'message' => 'Participation confirmée'
        ], JSON_UNESCAPED_UNICODE);

        $qrCode = Builder::create()
            ->data($qrData)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(200)
            ->margin(10)
            ->foregroundColor(new Color(0, 0, 0))
            ->backgroundColor(new Color(255, 255, 255))
            ->build();

        $qrCodeImage = base64_encode($qrCode->getString());

        // Génération du contenu HTML pour le PDF
        $html = $this->renderView('participation/event_pdf.html.twig', [
            'event' => $event,
            'qrCode' => $qrCodeImage,
            'participation' => $participation,
            'user' => $currentUser
        ]);

        // Configuration DomPDF
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Génération du fichier PDF
        $pdfOutput = $dompdf->output();
        $pdfFilename = 'ticket_' . $event->getTitre() . '.pdf';

        // Téléchargement du fichier PDF
        return new Response(
            $pdfOutput,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $pdfFilename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]
        );

    } catch (\Exception $e) {
        // Gestion des erreurs
        $this->logger->error('Erreur PDF : ' . $e->getMessage());
        $this->addFlash('error', 'Erreur lors de la génération du ticket');
        return $this->redirectToRoute('app_event_details', ['id' => $id_event]);
    }
}

#[Route('/mes-participations', name: 'app_mes_participations')]
#[IsGranted('ROLE_USER')]
public function mesParticipations(): Response
{
    $user = $this->getUser();
    $participations = $this->em->getRepository(Participation::class)->findBy(['id_user' => $user]);

    return $this->render('participation/mes_participations.html.twig', [
        'participations' => $participations
    ]);
}

#[Route('/annuler-participation/{id}', name: 'app_cancel_participation', methods: ['POST'])]
#[IsGranted('ROLE_USER')]
public function cancelParticipation(Participation $participation, Request $request): Response
{
    $user = $this->getUser();

    if ($participation->getId_user() !== $user) {
        $this->addFlash('error', 'Action non autorisée');
        return $this->redirectToRoute('app_mes_participations');
    }

    if ($this->isCsrfTokenValid('cancel'.$participation->getIdparticipation(), $request->request->get('_token'))) {
        $this->em->remove($participation);
        $this->em->flush();

        $this->addFlash('success', 'Participation annulée avec succès');
    }

    return $this->redirectToRoute('app_mes_participations');
}


#[Route('/export-participations/event/{id}', name: 'export_event_participants')]
#[IsGranted('ROLE_USER')]
public function exportEventParticipations(int $id): Response
{
    $event = $this->em->getRepository(Event::class)->find($id);
    
    if (!$event) {
        throw $this->createNotFoundException('Événement non trouvé');
    }

    $participations = $this->em->getRepository(Participation::class)->findBy(['idevent' => $event]);

    if (empty($participations)) {
        throw $this->createNotFoundException('Aucune participation trouvée');
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Configuration générale
    $sheet->getDefaultRowDimension()->setRowHeight(22);
    $sheet->setTitle('Participants ' . $event->getTitre());

    // Style des en-têtes
    $headerStyle = [
        'font' => [
            'bold' => true, 
            'color' => ['argb' => 'FFFFFFFF'], 
            'size' => 12
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID, 
            'startColor' => ['argb' => 'FF4CAF50']
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN, 
                'color' => ['argb' => 'FF000000']
            ]
        ],
    ];

    // En-têtes avec largeurs personnalisées
    $headers = [
        'A' => ['Nom', 25],
        'B' => ['Prénom', 25],
        'C' => ['Email', 40],
        'D' => ['Titre événement', 30],
        'E' => ['Date', 20],
        'F' => ['Lieu', 30]
    ];

    foreach ($headers as $col => [$title, $width]) {
        $sheet->setCellValue($col . '1', $title)
              ->getColumnDimension($col)->setWidth($width);
    }

    $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

    // Style des données
    $dataStyle = [
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_LEFT,
            'vertical' => Alignment::VERTICAL_CENTER
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN, 
                'color' => ['argb' => 'FF000000']
            ]
        ],
    ];

    // Remplissage des données avec bandes alternées
    $row = 2;
    foreach ($participations as $index => $participation) {
        $user = $participation->getId_user();
        $event = $participation->getIdevent();

        // Alternance de couleurs
        $fillColor = $index % 2 ? 'F5F6FA' : 'FFFFFF';
        
        $sheet->getStyle('A' . $row . ':F' . $row)
            ->applyFromArray($dataStyle)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF' . $fillColor));

        // Formatage date Excel
        $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(
            $event->getDate()
        );

        $sheet->setCellValue('A' . $row, $user->getNomUser())
              ->setCellValue('B' . $row, $user->getPrenomUser())
              ->setCellValue('C' . $row, $user->getEmailUser())
              ->setCellValue('D' . $row, $event->getTitre())
              ->setCellValue('E' . $row, $excelDate)
              ->setCellValue('F' . $row, $event->getLieu());

        // Formatage date français
        $sheet->getStyle('E' . $row)
            ->getNumberFormat()
            ->setFormatCode('dd/mm/yyyy hh:mm');

        $row++;
    }

    // Options avancées
    $sheet->setAutoFilter('A1:F' . ($row - 1));
    $sheet->freezePane('A2');
    
    // Centrage des colonnes
    $sheet->getStyle('E:E')->getAlignment()
        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Génération du fichier
    $writer = new Xlsx($spreadsheet);
    $tempFile = tempnam(sys_get_temp_dir(), 'participants_');
    $writer->save($tempFile);

    return $this->file(
        $tempFile,
        sprintf('participants_%s_%s.xlsx', $event->getTitre(), date('Ymd-His')),
        ResponseHeaderBag::DISPOSITION_ATTACHMENT
    );
}
}