<?php

namespace App\MessageHandler;

use App\Message\DeleteImagePost;
use App\Photo\PhotoFileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeleteImagePostHandler
{
    public function __construct(
        private PhotoFileManager       $photoManager,
        private EntityManagerInterface $entityManager
    )
    {

    }

    public function __invoke(DeleteImagePost $deleteImagePost)
    {
        $imagePost = $deleteImagePost->getImagePost();
        $this->photoManager->deleteImage($imagePost->getFilename());
        $this->entityManager->remove($imagePost);
        $this->entityManager->flush();
    }
}
