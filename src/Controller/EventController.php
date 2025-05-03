<?php
// src/Controller/EventController.php

namespace App\Controller;


use App\Entity\Event;
use App\Form\EventType;
use App\Entity\Participation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

class EventController extends AbstractController
{
    // Back office routes
    #[Route('/event', name: 'app_dashboard_event')]
    public function adminIndex(EntityManagerInterface $em): Response
    {
        $events = $em->getRepository(Event::class)->findAll();

        return $this->render('event/event_back.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('event/new', name: 'app_event_ajout')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event->setIdUser($this->getUser());
            $em->persist($event);
            $em->flush();

            $this->addFlash('success', 'Événement créé avec succès!');
            return $this->redirectToRoute('app_dashboard_event');
        }

        return $this->render('event/ajout_event_back.html.twig', [
            'form' => $form->createView(),
            'is_edit' => false
        ]);
    }

    #[Route('event/{id}/edit', name: 'app_event_edit')]
    public function edit(Request $request, Event $event, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Événement mis à jour!');
            return $this->redirectToRoute('app_dashboard_event');
        }

        return $this->render('event/ajout_event_back.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'is_edit' => true
        ]);
    }

    #[Route('event/{id}/delete', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $event->getIdevent(), $request->request->get('_token'))) {
            $em->remove($event);
            $em->flush();
            $this->addFlash('success', 'Événement supprimé!');
        }

        return $this->redirectToRoute('app_dashboard_event');
    }

    // Front office routes
    #[Route('/event/front', name: 'app_events_front')]
    public function frontIndex(EntityManagerInterface $em): Response
    {
        $events = $em->getRepository(Event::class)->findBy([], ['date' => 'ASC']);

        return $this->render('event/events_front.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('event/front/{id}/details', name: 'app_event_details', methods: ['GET'])]
    public function eventDetails(Event $event): Response
    {
        return $this->render('event/event_front_details.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('event/front/{id}/participate', name: 'app_event_participate', methods: ['POST'])]
    public function participate(Event $event, EntityManagerInterface $em, Security $security): JsonResponse
    {
        $user = $security->getUser();
        
        if (!$user) {
            return $this->json([
                'success' => false, 
                'message' => 'Vous devez être connecté pour participer'
            ], 401);
        }
        
        // Vérifier si l'utilisateur participe déjà
        $existingParticipation = $em->getRepository(Participation::class)->findOneBy([
            'idevent' => $event,
            'id_user' => $user
        ]);
        
        if ($existingParticipation) {
            return $this->json([
                'success' => false, 
                'message' => 'Vous participez déjà à cet événement'
            ]);
        }
        
        // Vérifier la capacité
        if ($event->getParticipations()->count() >= $event->getCapacite()) {
            return $this->json([
                'success' => false, 
                'message' => 'Cet événement est complet'
            ]);
        }
        
        // Créer la participation
        $participation = new Participation();
        $participation->setIdevent($event);
        $participation->setId_user($user);
        
        $em->persist($participation);
        $em->flush();
        
        return $this->json([
            'success' => true,
            'message' => 'Participation enregistrée avec succès',
            'participantsCount' => $event->getParticipations()->count(),
            'capacity' => $event->getCapacite()
        ]);
    }

    #[Route('event/front/{id}/cancel-participation', name: 'app_event_cancel_participation', methods: ['POST'])]
    public function cancelParticipation(Event $event, EntityManagerInterface $em, Security $security): JsonResponse
    {
        $user = $security->getUser();
        
        if (!$user) {
            return $this->json([
                'success' => false, 
                'message' => 'Vous devez être connecté'
            ], 401);
        }
        
        $participation = $em->getRepository(Participation::class)->findOneBy([
            'idevent' => $event,
            'id_user' => $user
        ]);
        
        if (!$participation) {
            return $this->json([
                'success' => false, 
                'message' => 'Vous ne participez pas à cet événement'
            ]);
        }
        
        $em->remove($participation);
        $em->flush();
        
        return $this->json([
            'success' => true,
            'message' => 'Participation annulée avec succès',
            'participantsCount' => $event->getParticipations()->count(),
            'capacity' => $event->getCapacite()
        ]);
    }

    #[Route('event/front/{id}/participation-stats', name: 'app_event_participation_stats', methods: ['GET'])]
    public function participationStats(Event $event): JsonResponse
    {
        $capacity = $event->getCapacite();
        $currentParticipants = $event->getParticipations()->count();
        $participationPercentage = $capacity > 0 ? ($currentParticipants / $capacity) * 100 : 0;

        return $this->json([
            'success' => true,
            'currentParticipants' => $currentParticipants,
            'capacity' => $capacity,
            'participationPercentage' => $participationPercentage
        ]);
    }

    #[Route('/generate/description', name: 'app_generate_description', methods: ['POST'])]
    public function generateDescription(
        Request $request,
        HttpClientInterface $httpClient,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $prompt = trim($data['prompt'] ?? '');

        // Validation des données
        $constraints = new Assert\Collection([
            'prompt' => [
                new Assert\NotBlank(['message' => 'Le prompt ne peut pas être vide']),
                new Assert\Length([
                    'max' => 100,
                    'maxMessage' => 'Le prompt ne peut dépasser {{ limit }} caractères'
                ]),
                new Assert\Regex([
                    'pattern' => '/^[^<>]*$/',
                    'message' => 'Les balises HTML sont interdites'
                ])
            ]
        ]);

        $errors = $validator->validate(['prompt' => $prompt], $constraints);
        if (count($errors) > 0) {
            return $this->json([
                'error' => (string) $errors->get(0)->getMessage()
            ], 400);
        }

        // Limite de requêtes
        $cache = new FilesystemAdapter();
        $clientIp = $request->getClientIp();
        $cacheKey = 'ai_gen_' . md5($clientIp);
        $counter = $cache->getItem($cacheKey);

        if (!$counter->isHit()) {
            $counter->set(1);
            $counter->expiresAfter(60);
        } else {
            $counter->set($counter->get() + 1);
        }
        $cache->save($counter);

        if ($counter->get() > 5) {
            return $this->json([
                'error' => 'Trop de requêtes. Veuillez patienter 1 minute.'
            ], 429);
        }

        try {
            // Appel à l'API Hugging Face
            $response = $httpClient->request('POST', 'https://api-inference.huggingface.co/models/huggingface', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getParameter('huggingface_api_token'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'inputs' => "Génère une description détaillée en français pour un événement sportif sur le thème : '$prompt'",
                    'parameters' => [
                        'max_new_tokens' => 200,
                        'temperature' => 0.7,
                        'top_k' => 50,
                        'top_p' => 0.9,
                        'repetition_penalty' => 1.2
                    ]
                ],
                'timeout' => 25
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Erreur API: ' . $response->getStatusCode() . ' - ' . $response->getContent(false));
            }

            $content = $response->toArray();
            $generatedText = $content[0]['generated_text'] ?? '';

            if (empty($generatedText)) {
                throw new \Exception('Aucune description générée par l\'API.');
            }

            return $this->json([
                'content' => $this->sanitizeGeneratedText($generatedText),
                'usage' => $content['details'] ?? []
            ]);

        } catch (ClientExceptionInterface $e) {
            return $this->json([
                'error' => 'Erreur de communication avec l\'API : ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Une erreur interne est survenue : ' . $e->getMessage()
            ], 500);
        }
    }

    private function sanitizeGeneratedText(string $text): string
    {
        $cleanText = strip_tags($text);
        $cleanText = htmlspecialchars($cleanText, ENT_QUOTES, 'UTF-8');
        $cleanText = preg_replace('/\n+/', "\n", $cleanText);
        $cleanText = preg_replace('/\s+/', ' ', $cleanText);
        return substr(trim($cleanText), 0, 500);
    }
    #[Route('/check-api-key', name: 'app_check_api_key')]
public function checkApiKey(): Response
{
    try {
        $token = $this->getParameter('huggingface_api_token');
        return new Response("Clé API détectée : " . substr($token, 0, 4) . '...');
    } catch (\Exception $e) {
        return new Response("ERREUR : " . $e->getMessage());
    }
}
}
