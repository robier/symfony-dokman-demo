<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\WordPopularityService;
use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Service\WordPopularityService
 */
final class WordPopularityServiceTest extends TestCase
{
    public function dataProvider(): Generator
    {
        yield 'PHP rocks!' => ['php', 9.99];

        yield 'JavaScript sucks!' => ['javaScript', 0.01];

        yield 'GO is ok!' => ['go', 6.35];

        yield 'Can not decide for typescript' => ['typeScript', 5.03];
    }

    /**
     * @dataProvider dataProvider()
     */
    public function testFetchingDataFromProvider(string $word, float $score): void
    {
        /** @var WordPopularityService\Provider\ProviderInterface&MockObject $mock */
        $mock = $this->createMock(WordPopularityService\Provider\ProviderInterface::class);

        $mock->method('fetchWordScore')
            ->willReturnCallback(static function(string $word) use ($score): WordPopularityService\WordScore{
                return new WordPopularityService\WordScore($word, $score);
            });

        $service = new WordPopularityService($mock);

        $response = $service->get($word);

        self::assertSame($word, $response->getTerm());
        self::assertSame($score, $response->getScore());
        self::assertNull($response->getCachedAt());
    }
}
