<?php

namespace App\Controller\Api;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class GameController extends AbstractController
{
    public function __construct(
        private GameRepository $gameRepository
    ) {
    }

    #[Route('/api/game', methods: 'GET')]
    public function index(Request $request): Response
    {
        // get all games, 10 per page
        return $this->json($this->gameRepository->getAllGamesPaginated($request->query->get('page', 1), 10));
    }

    #[Route('/api/game/{id}', methods: 'GET')]
    public function show($id, GameRepository $gameRepository): Response
    {
        // show specific game
        $game = $gameRepository->find($id);
        if ($game == null) {
            throw $this->createNotFoundException();
        }
        return $this->json($game->toArray());
    }
}
