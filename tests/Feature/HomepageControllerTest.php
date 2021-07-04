<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomepageControllerTest extends WebTestCase
{
    public function testPageIsOpening(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');
        $response = $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        self::assertSame(json_encode(['Hello!', ['Welcome to' => 'dokman demo application']]), $response);
    }
}
