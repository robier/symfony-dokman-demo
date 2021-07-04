<?php

declare(strict_types=1);

namespace App\Service\WordPopularityService\Provider;

use App\Service\WordPopularityService\WordScore;

/**
 * Dummy class that represents Twitter provider
 */
final class Twitter implements ProviderInterface
{
    public function fetchWordScore(string $word): WordScore
    {
        return new WordScore($word, (float)(rand(0, 8) . '.' . rand(10, 99)));
    }
}
