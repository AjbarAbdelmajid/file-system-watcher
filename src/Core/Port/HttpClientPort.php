<?php
namespace App\Core\Port;

interface HttpClientPort
{
    /**
     * Send a JSON payload via HTTP POST.
     *
     * @param string $url     The endpoint URL
     * @param array  $payload The data to send
     */
    public function postJson(string $url, array $payload): void;
}