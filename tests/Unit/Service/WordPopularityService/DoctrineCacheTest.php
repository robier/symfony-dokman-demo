<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\WordPopularityService;

use App\Entity\WordPopularity;
use App\Repository\WordPopularityRepository;
use App\Service\WordPopularityService;
use App\Service\WordPopularityService\Provider\ProviderInterface;
use DateTimeImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Service\WordPopularityService\DoctrineCache
 */
final class DoctrineCacheTest extends TestCase
{
    public function testWithEmptyDatabase(): void
    {
        /** @var ProviderInterface&MockObject $providerMock */
        $providerMock = $this->createMock(ProviderInterface::class);

        $providerMock
            ->method('fetchWordScore')
            ->willReturnCallback(static function(string $word):WordPopularityService\WordScore {
                return new WordPopularityService\WordScore($word, 5.0);
            });

        /** @var WordPopularityRepository&MockObject $repositoryMock */
        $repositoryMock = $this->createMock(WordPopularityRepository::class);

        $repositoryMock
            ->expects($this->once())
            ->method('findOneByWord')
            ->willReturn(null);

        $repositoryMock
            ->expects($this->once())
            ->method('createNew');

        $provider = new WordPopularityService\DoctrineCache($providerMock, $repositoryMock, 500);

        $wordScore = $provider->fetchWordScore('test');

        self::assertSame('test', $wordScore->getTerm());
        self::assertSame(5.0, $wordScore->getScore());
        self::assertNull($wordScore->getCachedAt());
    }

    public function testWithDataFromDatabase(): void
    {
        /** @var ProviderInterface&MockObject $providerMock */
        $providerMock = $this->createMock(ProviderInterface::class);

        $providerMock
            ->method('fetchWordScore')
            ->willReturnCallback(static function(string $word):WordPopularityService\WordScore {
                return new WordPopularityService\WordScore($word, 5.0);
            });

        /** @var WordPopularityRepository&MockObject $repositoryMock */
        $repositoryMock = $this->createMock(WordPopularityRepository::class);

        $entity = new WordPopularity();
        $entity->setWord('test')->setScore(5.0)->setCratedAt(new DateTimeImmutable());

        $repositoryMock
            ->method('findOneByWord')
            ->willReturn($entity);

        $repositoryMock
            ->expects($this->never())
            ->method('createNew');

        $repositoryMock
            ->expects($this->never())
            ->method('update');

        $provider = new WordPopularityService\DoctrineCache($providerMock, $repositoryMock, 500);

        $wordScore = $provider->fetchWordScore('test');

        self::assertSame('test', $wordScore->getTerm());
        self::assertSame(5.0, $wordScore->getScore());
        self::assertInstanceOf(DateTimeImmutable::class, $wordScore->getCachedAt());
    }

    public function testWithDataFromDatabaseThatExpired(): void
    {
        /** @var ProviderInterface&MockObject $providerMock */
        $providerMock = $this->createMock(ProviderInterface::class);

        $providerMock
            ->method('fetchWordScore')
            ->willReturnCallback(static function(string $word):WordPopularityService\WordScore {
                return new WordPopularityService\WordScore($word, 6.0);
            });

        /** @var WordPopularityRepository&MockObject $repositoryMock */
        $repositoryMock = $this->createMock(WordPopularityRepository::class);

        $entity = new WordPopularity();
        $entity
            ->setWord('test')
            ->setScore(5.0)
            ->setCratedAt(
                (new DateTimeImmutable())
                    ->modify('-10 days')
            );

        $repositoryMock
            ->method('findOneByWord')
            ->willReturn($entity);

        $repositoryMock
            ->expects($this->never())
            ->method('createNew');

        $repositoryMock
            ->expects($this->once())
            ->method('update');

        $provider = new WordPopularityService\DoctrineCache($providerMock, $repositoryMock, 30);

        $wordScore = $provider->fetchWordScore('test');

        self::assertSame('test', $wordScore->getTerm());
        self::assertSame(6.0, $wordScore->getScore());
        self::assertNull($wordScore->getCachedAt());
    }
}
