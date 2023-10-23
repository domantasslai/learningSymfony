<?php

namespace App\Service;

use App\Controller\QuestionController;
use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class VotingService
{
    public function __construct(private EntityManagerInterface $entityManager, private LoggerInterface $logger)
    {
    }

    public function vote(Question|Answer $entity, string $direction): Question|Answer
    {
        if ($direction === 'up') {
            $this->logger->info('Voting up!');
            $entity->upVote();
        } elseif ($direction === 'down') {
            $this->logger->info('Voting down!');
            $entity->downVote();
        }

        $this->entityManager->flush();

        return $entity;
    }
}
