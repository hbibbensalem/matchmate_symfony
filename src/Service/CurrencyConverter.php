<?php
namespace App\Service;


use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CurrencyConverter
{

    public function __construct(
        private HttpClientInterface $client,
        #[Autowire('%env(EXCHANGE_RATE_API_KEY)%')]
        private string $apiKey    ) {
            
        }

    public function convert(float $amount, string $from, string $to): float
    {
        try {
            $response = $this->client->request(
                'GET',
                "https://v6.exchangerate-api.com/v6/{$this->apiKey}/pair/{$from}/{$to}"
            );

            $data = $response->toArray();
            return $amount * $data['conversion_rate'];
        } catch (\Exception $e) {
            // En cas d'erreur, retourne le montant original
            return $amount;
        }
    }
}