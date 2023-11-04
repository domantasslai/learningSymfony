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
        $updatedContents = $this->ponkaficator->ponkafy(
            $this->photoManager->read($addPonkaToImage->getImagePost()->getFilename())
        );

        $this->photoManager->update($addPonkaToImage->getImagePost()->getFilename(), $updatedContents);
        $addPonkaToImage->getImagePost()->markAsPonkaAdded();
        $this->entityManager->flush();
    }
}
