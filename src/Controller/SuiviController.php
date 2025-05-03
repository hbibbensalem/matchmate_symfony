<?php

namespace App\Controller;

use App\Entity\Suivi;
use App\Form\SuiviType; // Ensure this class exists in the App\Form namespace
use App\Entity\User;
use App\Entity\Regime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/suivi', name: 'app_suivi_')]
class SuiviController extends AbstractController
{
    #[Route('/chat', name: 'chat', methods: ['POST'])]
    public function chat(Request $request, HttpClientInterface $httpClient): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $message = $data['message'] ?? '';
        
        try {
            $response = $httpClient->request('POST', 'http://127.0.0.1:5000/chat', [
                'json' => ['message' => $message]
            ]);
            
            return new JsonResponse($response->toArray());
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
    
    #[Route('/transcribe', name: 'transcribe', methods: ['POST'])]
    public function transcribe(Request $request, HttpClientInterface $httpClient): JsonResponse
    {
        try {
            $audioFile = $request->files->get('audio');
    
            if (!$audioFile) {
                return new JsonResponse(['error' => 'No audio file provided'], 400);
            }
    
            $response = $httpClient->request('POST', 'http://127.0.0.1:5000/transcribe', [
                'headers' => [
                    'Content-Type' => 'multipart/form-data',
                ],
                'body' => [
                    'file' => fopen($audioFile->getPathname(), 'rb')
                ],
            ]);
    
            return new JsonResponse($response->toArray());
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $suivi = new Suivi();
        $form = $this->createForm(SuiviType::class, $suivi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                
                $em->persist($suivi);
                $em->flush();
                $this->addFlash('success', 'Suivi #'.$suivi->getSuivi_id().' créé avec succès!');
                return $this->redirectToRoute('app_suivi_new');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur: '.$e->getMessage());
            }
        }

        return $this->render('suivi/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/list', name: 'list')]
    public function list(EntityManagerInterface $em): Response
    {
        $suivis = $em->getRepository(Suivi::class)->findAll();
        
        return $this->render('suivi/list.html.twig', [
            'suivis' => $suivis
        ]);
    }

    #[Route('/{suivi_id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Suivi $suivi, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SuiviType::class, $suivi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em->flush();
                $this->addFlash('success', 'Suivi modifié avec succès');
                return $this->redirectToRoute('app_suivi_list');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur: '.$e->getMessage());
            }
        }

        return $this->render('suivi/edit.html.twig', [
            'suivi' => $suivi,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{suivi_id}', name: 'delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        Suivi $suivi, 
        EntityManagerInterface $em
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$suivi->getSuivi_id(), $request->request->get('_token'))) {
            try {
                $em->remove($suivi);
                $em->flush();
                $this->addFlash('success', 'Suivi supprimé avec succès');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur: '.$e->getMessage());
            }
        }

        return $this->redirectToRoute('app_suivi_list');
    }

    #[Route('/MesSuivis', name: 'front')]
    public function listFront(EntityManagerInterface $em): Response
    {    
        $userId = 1;

        $suivis = $em->getRepository(Suivi::class)->findBy(['id_user' => $userId]);

        return $this->render('suivi/listFront.html.twig', [
            'suivis' => $suivis
        ]);
    }

    #[Route('/{suivi_id}', name: 'delete_front', methods: ['POST'])]
    public function deleteFront(
        Request $request,
        int $suivi_id,
        EntityManagerInterface $em
    ): Response {
        $suivi = $em->getRepository(Suivi::class)->find($suivi_id);
        
        if (!$suivi) {
            throw $this->createNotFoundException('Suivi non trouvé');
        }

        if ($this->isCsrfTokenValid('delete'.$suivi_id, $request->request->get('_token'))) {
            try {
                $em->remove($suivi);
                $em->flush();
                $this->addFlash('success', 'Suivi supprimé avec succès');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur: '.$e->getMessage());
            }
        }

        return $this->redirectToRoute('app_suivi_front');
    }

    #[Route('/backsuivi', name: 'backsuivi')]
    public function listBack(EntityManagerInterface $em): Response
    {    
        $userId = 1; 
        $suivis = $em->getRepository(Suivi::class)->findBy(['id_user' => $userId]);

        return $this->render('suivi/listbacksuivi.html.twig', [
            'suivis' => $suivis
        ]);
    }
   
    #[Route('/suivis/user/{id}', name: 'list_by_user')]
public function listByUserSuivi(string $id, EntityManagerInterface $em): Response
{
    $user = $em->getRepository(User::class)->find($id);

    if (!$user) {
        throw $this->createNotFoundException('Utilisateur non trouvé');
    }

    $suivis = $em->getRepository(Suivi::class)->findBy(['id_user' => $id]);

    return $this->render('suivi/list.html.twig', [
        'user' => $user,
        'suivis' => $suivis,
    ]);
}

#[Route('/backsuivi/user/{id}', name: 'list_by_user_back')]
public function listByUserSuiviBack(string $id, EntityManagerInterface $em): Response
{
    $user = $em->getRepository(User::class)->find($id);

    if (!$user) {
        throw $this->createNotFoundException('Utilisateur non trouvé');
    }

    $suivis = $em->getRepository(Suivi::class)->findBy(['id_user' => $id]);

    return $this->render('suivi/listbacksuivi.html.twig', [
        'user' => $user,
        'suivis' => $suivis,
    ]);
}

 
    

}