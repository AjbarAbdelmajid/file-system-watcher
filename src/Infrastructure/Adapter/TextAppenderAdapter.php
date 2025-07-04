<?php

namespace App\Infrastructure\Adapter;

use App\Core\Port\TextAppenderPort;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TextAppenderAdapter implements TextAppenderPort
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $baconApiUrl
    ) {
    }

    /**
     * Fetch random paragraphs from Bacon Ipsum and append them to the .txt file.
     *
     * @param string $filePath Absolute path to the text file
     */
    public function appendRandomText(string $filePath): void
    {
        $response = $this->client->request('GET', $this->baconApiUrl);
        $data = $response->toArray();

        // Bacon Ipsum returns an array of paragraphs
        $textToAppend = "\n\n" . implode("\n\n", $data);

        file_put_contents($filePath, $textToAppend, FILE_APPEND);
    }
}
