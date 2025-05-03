<?php
namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Commande;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Service\OrderMailer;



class PaymentProduitController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    #[Route('/api/payment/create-session', name: 'payment_create_session', methods: ['POST'])]
    public function createSession(Request $request, ProduitRepository $produitRepo): JsonResponse
    {
        
        try {
            $data = json_decode($request->getContent(), true);
            
            if (empty($data['product_id'])) {
                throw new \Exception('Product ID manquant');
            }
    
            $produit = $produitRepo->find($data['product_id']);
            if (!$produit) {
                throw new \Exception('Produit introuvable');
            }
    
            \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
            
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => ['name' => $produit->getNomProduit()],
                        'unit_amount' => (int)($produit->getPrixProduit() * 100),
                    ],
                    'quantity' => $data['quantity'] ?? 1,
                ]],
                'mode' => 'payment',
                'success_url' => $this->generateUrl('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL).'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $this->generateUrl('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'metadata' => [
                    'product_id' => $data['product_id'],
                    'quantity' => $data['quantity'] ?? 1
                ]
            ]);
    
            return new JsonResponse(['id' => $session->id]);
    
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }



    #[Route('/payment/success', name: 'payment_success')]
    public function success(
        Request $request,
        EntityManagerInterface $em,
        ProduitRepository $produitRepository,
        OrderMailer $orderMailer
    ): Response {
        $sessionId = $request->query->get('session_id');
        
        try {
            \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            
            // 1. Récupération des données
            $productId = $session->metadata->product_id;
            $quantity = $session->metadata->quantity;
            $user = $this->getUser();
    
            if (!$user instanceof User) {
                return $this->redirectToRoute('app_login');
            }
    
            // 2. Récupération du produit
            $produit = $produitRepository->find($productId);
            if (!$produit) {
                throw new \Exception("Produit introuvable");
            }
    
            // 3. Création de la commande (sans stripeSessionId)
            $commande = (new Commande())
                ->setProduit($produit)
                ->setQuantiteCommande($quantity)
                ->setDateCommande(new \DateTime())
                ->setStatusCommande('VALIDEE') // Statut différent pour paiement en ligne
                ->setUser($user);
    
            // 4. Mise à jour du stock
            $produit->setQuantiteProduit($produit->getQuantiteProduit() - $quantity);
    
            // 5. Sauvegarde
            $em->persist($commande);
            $em->persist($produit);
            $em->flush();
            // 6. Envoi de l'email de confirmation
            $orderMailer->sendConfirmation($commande);

            return $this->render('payment_produit/success.html.twig', [
                'session' => $session,
                'redirectUrl' => $this->generateUrl('app_commande_commandeFront')
            ]);
    
        } catch (\Exception $e) {
            // Vous pouvez logger l'erreur ici si besoin
            return $this->redirectToRoute('payment_cancel');
        }
    }
    


    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
        return $this->render('payment_produit/cancel.html.twig', [
            'productsUrl' => $this->generateUrl('app_produit_front')
        ]);
    }
}