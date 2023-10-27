<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends BaseController
{
    #[Route('/api/me', name: 'app_user_api_me')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function apiMe(): Response
    {
        return $this->json(data: $this->getUser(), context: [
            'groups' => ['user:read']
        ]);
    }
}
