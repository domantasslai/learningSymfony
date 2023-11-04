<?php

namespace App\MessageHandler;

use App\Message\AddPonkaToImage;
use App\Photo\PhotoFileManager;
use App\Photo\PhotoPonkaficator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AddPonkaToImageHandler
{
    public function __construct(private PhotoPonkaficator $ponkaficator, private PhotoFileManager $photoManager, private EntityManagerInterface $entityManager)
    {

    }

    public function __invoke(AddPonkaToImage $addPonkaToImage)
    {
        $imagePost = $addPonkaToImage->getImagePost();
        $updatedContents = $this->ponkaficator->ponkafy(
            $this->photoManager->read($imagePost->getFilename())
        );

        $this->photoManager->update($imagePost->getFilename(), $updatedContents);
        $imagePost->markAsPonkaAdded();
        $this->entityManager->persist($imagePost);
        $this->entityManager->flush();
    }
}
