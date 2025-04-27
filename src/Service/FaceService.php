<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class FaceService
{
    private HttpClientInterface $client;
    private string $apiKey;
    private string $apiSecret;

    public function __construct(HttpClientInterface $client, string $apiKey, string $apiSecret)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
    }

    public function compareFaces(string $image1Base64, string $image2Base64): bool
    {
        $response = $this->client->request('POST', 'https://api-us.faceplusplus.com/facepp/v3/compare', [
            'body' => [
                'api_key' => $this->apiKey,
                'api_secret' => $this->apiSecret,
                'image_base64_1' => $image1Base64,
                'image_base64_2' => $image2Base64,
            ],
            // Ajoute cette option pour ignorer la vérif SSL
            'verify_peer' => false,
            'verify_host' => false,
        ]);
        

        $data = $response->toArray();

        // Confidence entre 0 et 100
        return isset($data['confidence']) && $data['confidence'] > 75;
    }
}
