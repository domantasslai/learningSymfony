<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/comments/{id<\d+>}/vote/{direction<up|down>}", name="comments.vote", methods="POST")
     * @param $id
     * @param $direction
     * @param LoggerInterface $logger
     * @return JsonResponse
     */
    public function commentVote($id, $direction, LoggerInterface $logger): JsonResponse
    {
        // todo use id to query database
        $currentVoteCount = 0;

        if ($direction === 'up') {
            $currentVoteCount = rand(7, 100);
            $logger->info('Voting up!');

        } elseif ($direction === 'down') {
            $currentVoteCount = rand(0, 5);
            $logger->info('Voting down!');
        }

        return $this->json(['votes' => $currentVoteCount]);
    }
}
