<?php

declare(strict_types=1);

namespace App\Service\WordPopularityService;

use App\Repository\WordPopularityRepository;
use App\Service\WordPopularityService\Provider\ProviderInterface;
use DateTimeImmutable;

final class DoctrineCache implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $provider,
        private WordPopularityRepository $popularityRepository,
        private int $timeToLive
    )
    {
    }

    public function fetchWordScore(string $word): WordScore
    {
        // try get from database
        $result = $this->popularityRepository->findOneByWord($word);

        if ($result !== null) {
            $timestampDiff = ((new DateTimeImmutable())->getTimestamp() - ((int)$result->getCratedAt()?->getTimestamp()));
            if ($timestampDiff <= $this->timeToLive) {
                return new WordScore($word, $result->getScore(), $result->getCratedAt());
            }
        }

        // nothing in database, get from API and put in database for later use
        $wordScore = $this->provider->fetchWordScore($word);

        // save in database
        if ($result === null) {
            $this->popularityRepository->createNew($word, $wordScore->getScore());
        } else {
            $this->popularityRepository->update($result, $wordScore->getScore());
        }

        return $wordScore;
    }
}
