<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'homepage')]
final class HomepageController
{
    public function __invoke(): Response
    {
        return new JsonResponse(['Hello!', ['Welcome to' => 'dokman demo application']]);
    }
}
