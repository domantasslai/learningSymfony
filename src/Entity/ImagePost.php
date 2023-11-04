<?php

namespace App\Entity;

use App\Repository\ImagePostRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ImagePostRepository::class)]
class ImagePost
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    #[ORM\Column(length: 255)]
    private ?string $originalFilename = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $ponkaAddedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;
    }

    public function setOriginalFilename(string $originalFilename): static
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }

    public function getPonkaAddedAt(): ?\DateTimeImmutable
    {
        return $this->ponkaAddedAt;
    }

    public function markAsPonkaAdded(): static
    {
        $this->ponkaAddedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getImageFullPath(): string
    {
        return 'uploads/images/' . $this->filename;
    }
}
