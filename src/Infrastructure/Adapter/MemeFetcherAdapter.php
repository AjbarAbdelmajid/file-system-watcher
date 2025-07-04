<?php

namespace App\Infrastructure\Adapter;

use App\Core\Port\MemeFetcherPort;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class MemeFetcherAdapter implements MemeFetcherPort
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $memeApiUrl
    ) {
    }

    /**
     * Fetch a random meme from the API and save it to the target path.
     *
     * @param string $targetPath Absolute path where the meme image should be saved
     */
    public function fetchAndSave(string $targetPath): void
    {
        // First call: get JSON with meme URL
        $response = $this->client->request('GET', $this->memeApiUrl);
        $data = $response->toArray();

        if (!isset($data['url'])) {
            throw new \RuntimeException('Meme API did not return a URL');
        }

        // Second call: download the image bytes
        $imageContent = $this->client->request('GET', $data['url'])->getContent();

        // Ensure directory exists
        $dir = dirname($targetPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($targetPath, $imageContent);
    }
}
