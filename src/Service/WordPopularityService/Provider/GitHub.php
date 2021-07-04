<?php

declare(strict_types=1);

namespace App\Service\WordPopularityService\Provider;

use App\Service\WordPopularityService\WordScore;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class GitHub implements ProviderInterface
{
    private const GITHUB_API_URL = 'https://api.github.com/search/issues';

    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $githubClient)
    {
        $this->httpClient = $githubClient;
    }

    protected function makeRequest(string $word, string $type): int
    {
        $result = $this->httpClient->request('GET', self::GITHUB_API_URL, [
            'query' => [
                'q' => $word . ' ' . $type,
                'per_page' => 1,
                'page' => 1,
            ]
        ]);

        $decodedResult = json_decode($result->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return $decodedResult['total_count'] ?? 0;
    }

    public function fetchWordScore(string $word): WordScore
    {
        $rocks = $this->makeRequest($word, 'rocks');
        $sucks = $this->makeRequest($word, 'sucks');

        $sum = $rocks + $sucks;

        return new WordScore($word, round(($rocks / $sum) * 10, 2));
    }
}
