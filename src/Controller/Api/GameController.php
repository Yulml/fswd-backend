<?php

namespace App\Controller\Api;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class GameController extends AbstractController
{
    
    #[Route('/api/game', methods: 'GET')]
    public function index(Request $request, GameRepository $gameRepository, PaginatorInterface $paginator): Response
    {
        // show full list of games
        $currentPage = $request->query->get('page', 1);
        $query = $gameRepository->getQueryAll();
        $games = $paginator->paginate($query, $currentPage, 10);
        $result = [];
        foreach ($games as $game) {
            $platform = $game->getPlatform();
            $result[] = [
                'id' => $game->getId(),
                'name' => $game->getName(),
                'platform' => [
                    'id' => $platform->getId(),
                    'name' => $platform->getName(),
                  //  'cover' => $platform->getCover(),
                ] ,
              //  'genre' => $game->getGenre(),
            ];
        }
        return $this->json([
            'result' => $result
        ]);
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
