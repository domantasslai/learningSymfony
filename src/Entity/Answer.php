<?php

namespace App\Entity;

use App\Enum\AnswerStatus;
use App\Repository\AnswerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: AnswerRepository::class)]
class Answer
{
    private AnswerStatus $statusCases;

    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column]
    private int $votes = 0;

    #[ORM\ManyToOne(inversedBy: 'answers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Question $question = null;

    #[ORM\Column(length: 15)]
    private ?string $status;

    public function __construct()
    {
        $this->status = AnswerStatus::NEEDS_APPROVAL->value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getVotes(): int
    {
        return $this->votes;
    }

    public function setVotes(int $votes): static
    {
        $this->votes = $votes;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function getQuestionText(): string
    {
        if (!$this->getQuestion()) {
            return '';
        }

        return (string)$this->question->getQuestion();
    }

    public function setQuestion(?Question $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function upVote(): self
    {
        $this->votes++;

        return $this;
    }

    public function downVote(): self
    {
        $this->votes--;

        return $this;
    }

    public function getStatus(): ?AnswerStatus
    {
        return AnswerStatus::tryFrom($this->status);
    }

    public function setStatus(?string $status): static
    {
        $availableStatuses = array_column(AnswerStatus::cases(), 'value');

        if (!in_array($status, $availableStatuses)) {
            throw new \InvalidArgumentException(sprintf("Invalid status '%s'", $status));
        }

        $this->status = $status;

        return $this;
    }
}
