<?php

namespace App\Infrastructure\Adapter;

use App\Core\Port\HttpClientPort;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HttpClientAdapter implements HttpClientPort
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $endpoint
    ) {
    }

    /**
     * Send a JSON payload via HTTP POST to a fixed endpoint.
     *
     * @param string $unusedUrl Ignored, we use the injected endpoint
     * @param array  $payload  The data to send
     */
    public function postJson(string $unusedUrl, array $payload): void
    {
        $response = $this->client->request('POST', $this->endpoint, [
            'json' => $payload,
        ]);

        // Optionally check status:
        if ($response->getStatusCode() >= 400) {
            throw new \RuntimeException('JSON POST failed: ' . $response->getStatusCode());
        }
    }
}
