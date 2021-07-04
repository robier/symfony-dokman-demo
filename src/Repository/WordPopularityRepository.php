<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\WordPopularity;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WordPopularity|null find($id, $lockMode = null, $lockVersion = null)
 * @method WordPopularity|null findOneBy(array $criteria, array $orderBy = null)
 * @method WordPopularity[]    findAll()
 * @method WordPopularity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WordPopularityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WordPopularity::class);
    }

    public function findOneByWord(string $word): ?WordPopularity
    {
        return $this->createQueryBuilder('wp')
            ->andWhere('wp.word = :word')
            ->setParameter('word', $word)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function createNew(string $word, float $score): void
    {
        $entity = (new WordPopularity())
            ->setWord($word)
            ->setScore($score);

        $this->_em->persist($entity);
        $this->_em->flush();
    }

    public function update(WordPopularity $entity, float $score)
    {
        $entity->setScore($score);
        $entity->setCratedAt(new DateTimeImmutable());

        $this->_em->persist($entity);
        $this->_em->flush();
    }
}
