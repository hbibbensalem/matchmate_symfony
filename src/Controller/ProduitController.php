<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\ReferenceGenerator;
use App\Service\PdfGenerator;
use App\Entity\Commande;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\CurrencyConverter;
use App\Service\ExcelGenerator;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;




final class ProduitController extends AbstractController
{
    #[Route('/produit/ProduitDashboard', name: 'app_produit_dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        $produits = $em->getRepository(Produit::class)->findAll();
        $topProducts = $em->getRepository(Commande::class)
        ->createQueryBuilder('c')
        ->select([
            'p.nomProduit as productName', 
            'SUM(c.quantiteCommande) as totalQuantity'
        ])
        ->join('c.produit', 'p')
        ->where('c.statusCommande = :status')
        ->setParameter('status', 'VALIDEE')
        ->groupBy('p.idProduit')
        ->orderBy('totalQuantity', 'DESC')
        ->setMaxResults(5)
        ->getQuery()
        ->getResult();
    
    // Si aucun produit commandé, créer un tableau vide avec les noms des produits
    if (empty($topProducts)) {
        $topProducts = array_map(function($produit) {
            return [
                'productName' => $produit->getNomProduit(),
                'totalQuantity' => 0
            ];
        }, array_slice($produits, 0, 5));
    }
        
        return $this->render('produit/ProduitDashboard.html.twig', [
            'produits' => $produits,
            'topProducts' => $topProducts,
        ]);
    }

    #[Route('/produit/newProduit', name: 'app_produit_new')]
    public function new(
        Request $request, 
        EntityManagerInterface $em,
        ReferenceGenerator $referenceGenerator
    ): Response {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        
        // Retirer le champ refProduit du formulaire puisqu'il sera généré automatiquement
        $form->remove('refProduit');
    
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Générer la référence automatiquement
            $ref = $referenceGenerator->generateReference($produit->getNomProduit());
            $produit->setRefProduit($ref);
    
            // Gestion de l'upload d'image
            $imageFile = $form->get('imageProduit')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );
                $produit->setImageProduit($newFilename);
            }
    
            $em->persist($produit);
            $em->flush();
    
            $this->addFlash('success', 'Produit ajouté avec succès!');
            return $this->redirectToRoute('app_produit_dashboard', ['success' => true]);
        }
    
        return $this->render('produit/AjoutProduitBack.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/produit/{id}', name: 'app_produit_delete', methods: ['POST'])]
public function delete(Request $request, Produit $produit, EntityManagerInterface $em): Response
{
    if ($this->isCsrfTokenValid('delete'.$produit->getIdProduit(), $request->request->get('_token'))) {
        $em->remove($produit);
        $em->flush();
        $this->addFlash('success', 'Produit supprimé avec succès!');
    }

    return $this->redirectToRoute('app_produit_dashboard');
}

#[Route('/produit/{id}/editProduit', name: 'app_produit_edit')]
public function edit(
    Request $request, 
    Produit $produit, 
    EntityManagerInterface $em,
    ReferenceGenerator $referenceGenerator,
    \Psr\Log\LoggerInterface $logger
): Response {

    $logger->info('Début de la méthode edit');
    $ancienNom = $produit->getNomProduit();
    $ancienneImage = $produit->getImageProduit();
    
    $form = $this->createForm(ProduitType::class, $produit);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $logger->info('Formulaire soumis et valide');
        if ($produit->getNomProduit() !== $ancienNom) {
            $nouvelleRef = $referenceGenerator->generateReference($produit->getNomProduit());
            $produit->setRefProduit($nouvelleRef);
        }

        $keepImage = $request->request->get('keep_image', false);
        $logger->info('Keep image value: '.($keepImage ? 'true' : 'false'));
        $file = $form->get('imageProduit')->getData();
        
        if ($keepImage) {
            // Garder l'image existante
            $produit->setImageProduit($ancienneImage);
        } else {
            if ($file) {
                // Upload nouvelle image
                $filename = uniqid().'.'.$file->guessExtension();
                $file->move($this->getParameter('uploads_directory'), $filename);
                $produit->setImageProduit($filename);
            } else {
                // Pas de nouvelle image et case décochée
                $produit->setImageProduit(null);
            }
        }

        $em->flush();
        $this->addFlash('success', 'Produit mis à jour avec succès!');
        return $this->redirectToRoute('app_produit_dashboard');
    }

    return $this->render('produit/ModifProduitBack.html.twig', [
        'form' => $form->createView(),
        'produit' => $produit,
    ]);
}




#[Route('/produits/front', name: 'app_produit_front')]
public function frontProduit(EntityManagerInterface $em,Request $request, CurrencyConverter $currencyConverter): Response
{
    $produits = $em->getRepository(Produit::class)->findAll();
      // Récupérer les produits les plus commandés
      $topProducts = $em->getRepository(Commande::class)
      ->createQueryBuilder('c')
      ->select([
          'p.idProduit as productId',
          'p.nomProduit as productName', 
          'SUM(c.quantiteCommande) as totalQuantity'
      ])
      ->join('c.produit', 'p')
      ->where('c.statusCommande = :status')
      ->setParameter('status', 'VALIDEE')
      ->groupBy('p.idProduit')
      ->orderBy('totalQuantity', 'DESC')
      ->setMaxResults(3) // On prend les 3 meilleurs
      ->getQuery()
      ->getResult();

      $currency = $request->getSession()->get('currency', 'TND');
    

    
    return $this->render('produit/produitFront.html.twig', [
        'produits' => $produits,
        'topProducts' => $topProducts,
        'current_currency' => $currency,
        'currencyConverter' => $currencyConverter,
        'current_page' => 'produits',
        'page_title' => 'Produits - MatchMate'
    ]);
}

#[Route('/api/produit/{id}/details', name: 'api_produit_details', methods: ['GET'])]
public function getProduitDetails(Produit $produit, CurrencyConverter $currencyConverter, Request $request): JsonResponse
{
    $currentCurrency = $request->getSession()->get('currency', 'TND');
    $prix = $produit->getPrixProduit();
    
    if ($currentCurrency !== 'TND') {
        $prix = $currencyConverter->convert($prix, 'TND', $currentCurrency);
    }

    return $this->json([
        'id' => $produit->getIdProduit(),
        'nom' => $produit->getNomProduit(),
        'ref' => $produit->getRefProduit(),
        'prix' => $prix,
        'prixOriginal' => $produit->getPrixProduit(),
        'currency' => $currentCurrency,
        'image' => $produit->getImageProduit() ? 
                  $this->getParameter('uploads_directory').'/'.$produit->getImageProduit() : 
                  null,
        'stock' => $produit->getQuantiteProduit()
    ]);
}
#[Route('/convert/{currency}', name: 'convert_currency')]
public function convertCurrency(string $currency, Request $request): Response
{
    $request->getSession()->set('currency', $currency);
    return $this->redirectToRoute('app_produit_front');
}



#[Route('/produit/export-pdf', name: 'app_produit_export_pdf')]
public function exportToPdf(EntityManagerInterface $em, PdfGenerator $pdfGenerator): Response
{
    // Récupérer tous les produits
    $produits = $em->getRepository(Produit::class)->findAll();
    
    // Rendre le template Twig en HTML
    $html = $this->renderView('produit/export_pdf.html.twig', [
        'produits' => $produits,
        'date_export' => new \DateTime()
    ]);
    
    // Générer et retourner le PDF
    return $pdfGenerator->generatePdfFromHtml($html, 'liste_produits_'.date('Y-m-d').'.pdf');
}



#[Route('/api/generate-product-description', name: 'app_generate_product_description', methods: ['POST'])]
public function generateProductDescription(Request $request): JsonResponse
{
    $data = json_decode($request->getContent(), true);
    
    // Validation basique
    if (!isset($data['nomProduit'])) {
        return $this->json(['error' => 'Le nom du produit est requis'], 400);
    }

    $nomProduit = trim($data['nomProduit']);
    $currentDescription = $data['currentDescription'] ?? '';

    // Option 1: Génération simple (concaténation)
    $generatedDescription = $this->generateSmartDescription($nomProduit, $currentDescription);
    
  
    return $this->json([
        'generatedDescription' => $generatedDescription,
        'timestamp' => (new \DateTime())->format('Y-m-d H:i:s')
    ]);
}


private function generateSmartDescription(string $nomProduit, string $currentDescription = ''): string
{
    // Détecter le type de produit basé sur le nom
    $typeProduit = $this->detectProductType($nomProduit);
    
    // Templates de description par catégorie
    $templates = [
        'sport' => [
            "Découvrez notre équipement sportif %s, conçu pour les performances. %s",
            "Caractéristiques :\n- Matériaux techniques\n- Confort optimal\n- Durabilité accrue"
        ],
        'electronique' => [
            "%s - La technologie au service de votre quotidien. %s",
            "Spécifications :\n- Haute performance\n- Design ergonomique\n- Garantie 2 ans"
        ],
        'mode' => [
            "Collection %s : élégance et style intemporel. %s",
            "Détails :\n- Tissus de qualité\n- Coupe moderne\n- Entretien facile"
        ],
        'default' => [
            "Découvrez notre produit %s. %s",
            "Points forts :\n- Qualité garantie\n- Fabrication soignée\n- Satisfaction client"
        ]
    ];

    $template = $templates[$typeProduit] ?? $templates['default'];
    
    $baseDescription = sprintf(
        $template[0],
        $nomProduit,
        $currentDescription ? "Inspiré par : " . substr($currentDescription, 0, 80) . "..." : ""
    );

    return $baseDescription . "\n\n" . $template[1];
}

private function detectProductType(string $productName): string
{
    $keywords = [
        'sport' => ['ballon', 'raquette', 'running', 'sport', 'fitness'],
        'electronique' => ['smartphone', 'écran', 'pc', 'ordinateur', 'câble'],
        'mode' => ['chemise', 'robe', 'pantalon', 'chaussure', 'sac','tenue', 'vêtement']
    ];

    $productNameLower = strtolower($productName);
    
    foreach ($keywords as $type => $terms) {
        foreach ($terms as $keyword) {
            if (str_contains($productNameLower, $keyword)) {
                return $type;
            }
        }
    }
    
    return 'default';
}
#[Route('/produit/export-excel', name: 'app_produit_export_excel')]
public function exportToExcel(EntityManagerInterface $em, ExcelGenerator $excelGenerator): Response
{
    $produits = $em->getRepository(Produit::class)->findAll();
    $filename = 'liste_produits_'.date('Y-m-d').'.xlsx';
    
    return $excelGenerator->generateExcelFromProduits($produits, $filename);
}




}
