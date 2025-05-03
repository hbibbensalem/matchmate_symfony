<?php

namespace App\Controller;
use Dompdf\Dompdf;
use Doctrine\Persistence\ManagerRegistry;
use Dompdf\Options;
use App\Repository\UserRepository; 
use App\Entity\Regime;
use App\Entity\Suivi;
use App\Form\RegimeType; // Ensure this class exists in the App\Form namespace
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/regime', name: 'app_regime_')]
class RegimeController extends AbstractController
{
    #[Route('/new/{id}', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, UserRepository $userRepository, string $id): Response
    {
        $regime = new Regime();
    
        // Associer automatiquement l'utilisateur
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException("Utilisateur non trouvé.");
        }
        $regime->setIdUser($user); // Assure-toi que tu as cette relation dans l'entité Regime
    
        $form = $this->createForm(RegimeType::class, $regime);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            if (empty($regime->getStatut())) {
                $regime->setStatut('ACTIF');
            }
    
            try {
                $em->persist($regime);
                $em->flush();
                $this->addFlash('success', 'Régime #' . $regime->getRegimeId() . ' créé avec succès!');
                return $this->redirectToRoute('app_regime_list');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur: ' . $e->getMessage());
            }
        }
    
        return $this->render('regime/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/list', name: 'list')]
    public function list(EntityManagerInterface $em): Response
    {
        $regimes = $em->getRepository(Regime::class)->findAll();
        
        return $this->render('regime/list.html.twig', [
            'regimes' => $regimes
        ]);
    }

    #[Route('/{regime_id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Regime $regime, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RegimeType::class, $regime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em->flush();
                $this->addFlash('success', 'Régime modifié avec succès');
               
               return $this->redirectToRoute('app_regime_list');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur: '.$e->getMessage());
            }
        }

        return $this->render('regime/edit.html.twig', [
            'regime' => $regime,
            'form' => $form->createView(),
        
        ]);
    }

    #[Route('/{regime_id}', name: 'delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        Regime $regime, 
        EntityManagerInterface $em
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$regime->getRegimeId(), $request->request->get('_token'))) {
            try {
                $em->remove($regime);
                $em->flush();
                $this->addFlash('success', 'Régime supprimé avec succès');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur: '.$e->getMessage());
            }
        }

        return $this->redirectToRoute('app_regime_list');
    }

    #[Route('/mesRegimes', name: 'front')]
    public function listFront(EntityManagerInterface $em): Response
    {    
        // Récupérer l'utilisateur connecté (à adapter selon votre système d'authentification)
        // $user = $this->getUser();
        // Pour l'exemple, on utilise un ID statique comme dans votre CommandeController
        $userId = 1; // À remplacer par la récupération de l'ID utilisateur réel

        $regimes = $em->getRepository(Regime::class)->findBy(['id_user' => $userId]);

        return $this->render('regime/listFront.html.twig', [
            'regimes' => $regimes
        ]);
    }

    #[Route('/{regime_id}', name: 'delete_front', methods: ['POST'])]
    public function deleteFront(
        Request $request,
        int $regime_id,
        EntityManagerInterface $em
    ): Response {
        $regime = $em->getRepository(Regime::class)->find($regime_id);
        
        if (!$regime) {
            throw $this->createNotFoundException('Régime non trouvé');
        }

        if ($this->isCsrfTokenValid('delete'.$regime_id, $request->request->get('_token'))) {
            try {
                $em->remove($regime);
                $em->flush();
                $this->addFlash('success', 'Régime supprimé avec succès');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur: '.$e->getMessage());
            }
        }

        return $this->redirectToRoute('app_regime_front');
    }


    #[Route('/backregime', name: 'dashboard')]
    public function listBack(EntityManagerInterface $em): Response
    {    
        $userId = 1; 
        $regimes = $em->getRepository(Regime::class)->findAll();
        $stats = $em->createQueryBuilder()
        ->select('r.objectif, COUNT(r.regime_id) as total')
        ->from(Regime::class, 'r')
        ->where('r.id_user = :userId')
        ->setParameter('userId', $userId)
        ->groupBy('r.objectif')
        ->getQuery()
        ->getResult();
        return $this->render('regime/dashboard.html.twig', [
            'regimes' => $regimes
        ]);
    }


    #[Route('/backpremium', name: 'listpremium')]
    public function premiumPlayersBack(UserRepository $userRepository): Response
    {
        
        $users = $userRepository->findPremiumPlayers();
        
        if (empty($users)) {
            $this->addFlash('warning', 'Aucun joueur premium trouvé');
        } else {
            $this->addFlash('success', count($users).' joueur(s) premium trouvé(s)');
        }

        return $this->render('premium/listpremiumBack.html.twig', [
            'users' => $users,
        ]);
       
    }

    #[Route('/premium', name: 'listpremiumFront')]
    public function premiumPlayersFront(UserRepository $userRepository): Response
    {
        
        $users = $userRepository->findPremiumPlayers();
        
        if (empty($users)) {
            $this->addFlash('warning', 'Aucun joueur premium trouvé');
        } else {
            $this->addFlash('success', count($users).' joueur(s) premium trouvé(s)');
        }

        return $this->render('premium/listpremiumFront.html.twig', [
            'users' => $users,
        ]);
       
    }
    #[Route('/premium/pdf', name: 'pdf')]
public function generatePremiumPdf(UserRepository $userRepository): Response
{
    // Fetch the premium players (same as in premiumPlayersFront)
    $players = $userRepository->findPremiumPlayers();

    // Create an instance of Dompdf
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Arial');
    $dompdf = new Dompdf($pdfOptions);

    // Generate the HTML for the PDF from the Twig template
    $html = $this->renderView('premium/pdf.html.twig', [
        'players' => $players,
    ]);

    // Load the HTML into Dompdf
    $dompdf->loadHtml($html);

    // (Optional) Set paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render the PDF
    $dompdf->render();

    // Send the generated PDF to the browser
    return new Response(
        $dompdf->stream('liste_joueurs_premium.pdf', [
            'Attachment' => true // true = download directly; false = view in browser
        ])
    );
}

    #[Route('/regimes/user/{id}', name: 'list_by_user')]
    public function listByUser(string $id, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($id);
        
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
    
        $regimes = $em->getRepository(Regime::class)->findBy(['id_user' => $id]);
    
        return $this->render('regime/list.html.twig', [
            'user' => $user,
            'regimes' => $regimes,
        ]);


    }
    
    
    
   











}