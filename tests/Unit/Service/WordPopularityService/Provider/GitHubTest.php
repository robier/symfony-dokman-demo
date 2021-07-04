<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\WordPopularityService\Provider;

use App\Service\WordPopularityService\Provider\GitHub;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * @covers \App\Service\WordPopularityService\Provider\GitHub
 */
final class GitHubTest extends TestCase
{
    public function dataProvider(): Generator
    {
        yield 'Half good and half bad' => [
            [
                new MockResponse(json_encode(['total_count' => 10])),
                new MockResponse(json_encode(['total_count' => 10])),
            ],
            5.0
        ];

        yield '10 good and 7 bad' => [
            [
                new MockResponse(json_encode(['total_count' => 10])),
                new MockResponse(json_encode(['total_count' => 7])),
            ],
            5.88
        ];

        yield '0 good and 10 bad' => [
            [
                new MockResponse(json_encode(['total_count' => 0])),
                new MockResponse(json_encode(['total_count' => 10])),
            ],
            0
        ];

        yield '10 good and 0 bad' => [
            [
                new MockResponse(json_encode(['total_count' => 10])),
                new MockResponse(json_encode(['total_count' => 0])),
            ],
            10
        ];

        yield '985 good and 578 bad' => [
            [
                new MockResponse(json_encode(['total_count' => 985])),
                new MockResponse(json_encode(['total_count' => 578])),
            ],
            6.3
        ];
    }

    /**
     * @dataProvider dataProvider()
     */
    public function testFetchingData(array $responses, float $expectedSum): void
    {
        $mockClient = new MockHttpClient($responses);

        $provider = new GitHub($mockClient);

        $wordScore = $provider->fetchWordScore('test');

        self::assertSame($expectedSum, $wordScore->getScore());
    }
}
