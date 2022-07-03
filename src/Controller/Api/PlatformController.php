<?php

namespace App\Controller\Api;

use App\Entity\Platform;
use App\Repository\PlatformRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlatformController extends AbstractController
{
    public function __construct(
        private PlatformRepository $platformRepository
    ) {
    }

    #[Route('/api/platform', methods: 'GET')]
    
    public function index(Request $request): Response
    {
        // get all platforms, 10 per page
        return $this->json($this->platformRepository->getAllPlatformsPaginated($request->query->get('page', 1), 10));
    }
    
    #[Route('/api/platform/{platform}/game', methods: 'GET')]
    public function getGamesAction(Platform $platform): Response
    {
        return $this->json([
            'result' => $this->platformRepository->getPlatformGames($platform)
        ]);
    }

    
}
