<?php
namespace App\Controller;

use App\Entity\Commande;

use App\Entity\Produit;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Service\OrderMailer;


#[Route('/commande', name: 'app_commande_')]
class CommandeController extends AbstractController
{
    #[Route('/new', name: 'new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $em, LoggerInterface $logger,OrderMailer $orderMailer): JsonResponse
    {
        // Vérifier que la requête est AJAX
        if (!$request->isXmlHttpRequest()) {
            return $this->json([
                'success' => false,      
                'message' => 'Requête invalide'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            // 1. Vérification de l'utilisateur
            $user = $this->getUser();
            if (!$user instanceof User) {
                return $this->json([
                    'success' => false,
                    'message' => 'Vous devez être connecté pour passer une commande',
                    'redirect' => $this->generateUrl('app_login')
                ], Response::HTTP_UNAUTHORIZED);
            }

            // 2. Récupération et validation des données
            $data = json_decode($request->getContent(), true);
            
            if ($data === null) {
                return $this->json([
                    'success' => false,
                    'message' => 'Données JSON invalides'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Validation améliorée du token CSRF
            $submittedToken = $data['_token'] ?? '';
            if (!$this->isCsrfTokenValid('commande', $submittedToken)) {
                $logger->warning('Token CSRF invalide reçu', [
                    'received_token' => $submittedToken,
                    'expected_token' => $this->container->get('security.csrf.token_manager')->getToken('commande')->getValue()
                ]);
                
                return $this->json([
                    'success' => false,
                    'message' => 'Token de sécurité invalide'
                ], Response::HTTP_FORBIDDEN);
            }

            // 3. Validation des autres données
            $produitId = $data['idProduit'] ?? null;
            $quantite = (int)($data['quantiteCommande'] ?? 0);

            if (empty($produitId)) {
                return $this->json([
                    'success' => false,
                    'message' => 'Produit non spécifié'
                ], Response::HTTP_BAD_REQUEST);
            }

            $produit = $em->getRepository(Produit::class)->find($produitId);
            if (!$produit) {
                return $this->json([
                    'success' => false,
                    'message' => 'Produit introuvable'
                ], Response::HTTP_NOT_FOUND);
            }

            if ($quantite <= 0) {
                return $this->json([
                    'success' => false,
                    'message' => 'La quantité doit être supérieure à zéro'
                ], Response::HTTP_BAD_REQUEST);
            }

            if ($quantite > $produit->getQuantiteProduit()) {
                return $this->json([
                    'success' => false,
                    'message' => 'Quantité demandée non disponible. Stock restant: '.$produit->getQuantiteProduit()
                ], Response::HTTP_BAD_REQUEST);
            }

            // 4. Création de la commande
            $commande = (new Commande())
                ->setProduit($produit)
                ->setQuantiteCommande($quantite)
                ->setDateCommande(new \DateTime())
                ->setStatusCommande('VALIDEE')
                ->setUser($user);

            // Mise à jour du stock
            $produit->setQuantiteProduit($produit->getQuantiteProduit() - $quantite);

            $em->persist($commande);
            $em->persist($produit);
            $em->flush();
            
            $orderMailer->sendConfirmation($commande);
           

            return $this->json([
                'success' => true,
                'message' => 'Commande effectuée avec succès',
                'redirectUrl' => $this->generateUrl('app_commande_commandeFront'),
                'commandeId' => $commande->getIdCommande()
            ]);
    

        } catch (\Exception $e) {
            $logger->error('Erreur commande: '.$e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la commande: '.$e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/mesCommandes', name: 'commandeFront')]
    public function listFront(EntityManagerInterface $em): Response
    {    
        $user = $this->getUser();
        if (!$user instanceof User) {
            $this->addFlash('error', 'Vous devez être connecté pour voir vos commandes');
            return $this->redirectToRoute('app_login');
        }
    
        $commandes = $em->getRepository(Commande::class)->findBy(
            ['user' => $user],
            ['dateCommande' => 'DESC']
        );
    
        return $this->render('commande/listFront.html.twig', [
            'commandes' => $commandes,
            'current_page' => 'commandes'
        ]);
    }



  #[Route('/{idCommande}', name: 'delete', requirements: ['idCommande' => '\d+'], methods: ['POST'])]
public function delete(
    Request $request,
    Commande $commande,
    EntityManagerInterface $em
): Response {
    // Vérification CSRF
    if (!$this->isCsrfTokenValid('delete'.$commande->getIdCommande(), $request->request->get('_token'))) {
        $this->addFlash('error', 'Token de sécurité invalide');
        return $this->redirectToRoute('app_commande_commandeFront');
    }

    // Vérification des 48h
    $now = new \DateTime();
    $diff = $now->diff($commande->getDateCommande());
    $hours = $diff->h + ($diff->days * 24);
    
    if ($hours > 48) {
        $this->addFlash('error', 'Le délai d\'annulation de 48 heures est dépassé');
        return $this->redirectToRoute('app_commande_commandeFront');
    }

    // Restauration du stock
    $produit = $commande->getProduit();
    $produit->setQuantiteProduit($produit->getQuantiteProduit() + $commande->getQuantiteCommande());

    // Annulation de la commande
    $commande->setStatusCommande('ANNULEE');
    $em->flush();

    $this->addFlash('success', 'Commande annulée avec succès');
    return $this->redirectToRoute('app_commande_commandeFront');
}
   

#[Route('/listBack', name: 'listBack')]
public function list(EntityManagerInterface $em): Response
{
    // Debug: vérifiez d'abord ce que vous récupérez
    $commandes = $em->getRepository(Commande::class)->findAll();

      
    // Ajoutez temporairement ce dump pour vérification
    dump($commandes); 
    
    return $this->render('commande/listBack.html.twig', [
        'commandes' => $commandes,
        
    ]);
}

#[Route('/commandes/annulees', name: 'delete_annulees', methods: ['POST'])]
public function deleteAnnulees(Request $request, EntityManagerInterface $em): Response
{
    // 1. Vérification du token CSRF
    if (!$this->isCsrfTokenValid('delete_annulees', $request->request->get('_token'))) {
        $this->addFlash('error', 'Token de sécurité invalide');
        return $this->redirectToRoute('app_commande_listBack');
    }

    try {
        // 2. Récupération des commandes annulées
        $commandesAnnulees = $em->getRepository(Commande::class)
            ->findBy(['statusCommande' => 'ANNULEE']);

        if (empty($commandesAnnulees)) {
            $this->addFlash('warning', 'Aucune commande annulée à supprimer');
            return $this->redirectToRoute('app_commande_listBack');
        }

        // 3. Suppression en masse
        $count = 0;
        foreach ($commandesAnnulees as $commande) {
            $em->remove($commande);
            $count++;
        }

        $em->flush();

        // 4. Message de confirmation
        $this->addFlash('success', sprintf('%d commande(s) annulée(s) supprimée(s) avec succès!', $count));
        
    } catch (\Exception $e) {
        $this->addFlash('error', 'Une erreur est survenue lors de la suppression des commandes annulées');
        // Log l'erreur si vous avez un logger
        // $this->logger->error('Erreur suppression commandes annulées: '.$e->getMessage());
    }

    return $this->redirectToRoute('app_commande_listBack');
}

 

}