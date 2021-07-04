<?php

declare(strict_types=1);

namespace App\Service\WordPopularityService\Provider;

use App\Service\WordPopularityService\WordScore;

interface ProviderInterface
{
    /**
     * Fetch popularity score of the word from some source.
     */
    public function fetchWordScore(string $word): WordScore;
}
