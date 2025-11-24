<?php

namespace App\Service\OpenAi;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class OpenAi
{
    public function __construct(
        private string $apiKey,
        private HttpClientInterface $httpClient,
    ) {}

    protected function chat(string $systemPrompt, string $userPrompt): ResponseInterface
    {
        return $this->httpClient->request('POST', 'https://api.openai.com/v1/responses', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => json_encode([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt,
                    ],
                    [
                        'role' => 'user',
                        'content' => $userPrompt,
                    ]
                ]
            ])
        ]);
    }
}
