<?php

namespace App\Controller;

use App\Repository\ImagePostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main_homepage')]
    public function homepage(ImagePostRepository $imagePostRepository)
    {
        $images = $imagePostRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('main/homepage.html.twig', [
            'images' => $images
        ]);
    }
}
