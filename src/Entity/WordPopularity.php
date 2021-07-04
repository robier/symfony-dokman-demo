<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\WordPopularityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WordPopularityRepository::class)]
class WordPopularity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    private $word;

    #[ORM\Column(type: "decimal", precision: 3, scale: 2)]
    private $score;

    #[ORM\Column(type: "datetime_immutable")]
    private $cratedAt;

    public function __construct()
    {
        $this->setCratedAt(new \DateTimeImmutable());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWord(): ?string
    {
        return $this->word;
    }

    public function setWord(string $word): self
    {
        $this->word = $word;

        return $this;
    }

    public function getScore(): ?float
    {
        if ($this->score === null) {
            return null;
        }

        return (float)$this->score;
    }

    public function setScore(float $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getCratedAt(): ?\DateTimeImmutable
    {
        return $this->cratedAt;
    }

    public function setCratedAt(\DateTimeImmutable $cratedAt): self
    {
        $this->cratedAt = $cratedAt;

        return $this;
    }
}
