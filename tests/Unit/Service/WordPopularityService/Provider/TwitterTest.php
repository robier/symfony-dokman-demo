<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\WordPopularityService\Provider;

use App\Service\WordPopularityService\Provider\Twitter;
use App\Service\WordPopularityService\WordScore;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Service\WordPopularityService\Provider\Twitter
 */
final class TwitterTest extends TestCase
{
    public function testFetchingData(): void
    {
        $provider = new Twitter();

        $wordScore = $provider->fetchWordScore('test');

        self::assertInstanceOf(WordScore::class, $wordScore);
        self::assertSame('test', $wordScore->getTerm());
    }
}
