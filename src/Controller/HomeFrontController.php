<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeFrontController extends AbstractController
{
    #[Route('/home/front', name: 'app_home_front')]
    public function index(): Response
    {
        return $this->render('home_front/index.html.twig', [
            'stripe_public_key' => $_ENV['STRIPE_PUBLIC_KEY'],
            'controller_name' => 'HomeFrontController',
        ]);
    }
}
