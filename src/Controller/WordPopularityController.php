<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\WordPopularityService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/word-popularity/{word}', name: 'word-popularity')]
final class WordPopularityController
{
    public function __invoke(string $word, WordPopularityService $popularityService): Response
    {
        $wordScore = $popularityService->get($word);

        $age = 0;
        if ($wordScore->getCachedAt() !== null) {
            $age = (new DateTimeImmutable())->getTimestamp() - $wordScore->getCachedAt()->getTimestamp();
        }

        return new JsonResponse([
            'term' => $word,
            'score' => $wordScore->getScore(),
            'age' => $age,
        ]);
    }
}
