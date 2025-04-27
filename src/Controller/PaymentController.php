<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    #[Route('/create-checkout-session/{plan}', name: 'create_checkout_session')]
public function checkout(string $plan): JsonResponse
{
    try {
        \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd', // TEMPORAIRE : car TND n'est pas supportée
                    'product_data' => [
                        'name' => 'Abonnement ' . ucfirst($plan),
                    ],
                    'unit_amount' => $this->getPriceAmount($plan),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return new JsonResponse(['id' => $checkout_session->id]);

    } catch (\Exception $e) {
        return new JsonResponse([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
}


    private function getPriceAmount(string $plan): int
    {
        return match ($plan) {
            'basique' => 1200,
            'standard' => 2400,
            'premium' => 5000,
            'club' => 9900,
            default => throw new \Exception('Invalid plan'),
        };
    }

#[Route('/success', name: 'app_success')]
public function success(): Response
{
    return $this->render('payment/success.html.twig');
}

#[Route('/cancel', name: 'app_cancel')]
public function cancel(): Response
{
    return $this->render('payment/cancel.html.twig');
}

}
