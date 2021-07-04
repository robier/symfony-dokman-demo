<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\WordPopularityService\WordScore;

final class WordPopularityService
{
    public function __construct(private WordPopularityService\Provider\ProviderInterface $provider)
    {
    }

    public function get(string $word): WordScore
    {
        return $this->provider->fetchWordScore($word);
    }
}
