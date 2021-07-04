<?php

declare(strict_types=1);

namespace App\Service\WordPopularityService;

use DateTimeImmutable;

final class WordScore
{
    public function __construct(
        private string $term,
        private float $score,
        private ?DateTimeImmutable $cachedAt = null
    )
    {
    }

    public function getTerm(): string
    {
        return $this->term;
    }

    public function getScore(): float
    {
        return $this->score;
    }

    public function getCachedAt(): ?DateTimeImmutable
    {
        return $this->cachedAt;
    }
}
