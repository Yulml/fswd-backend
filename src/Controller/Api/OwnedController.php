<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\OwnedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class OwnedController extends AbstractController
{
    #[Route('/api/owned', methods: 'GET')]
    public function index(Request $request, OwnedRepository $ownedRepository, PaginatorInterface $paginator): Response
    {
        // show full list of owned games
        $currentPage = $request->query->get('page', 1);
        $query = $ownedRepository->getQueryAll();
        $owneds = $paginator->paginate($query, $currentPage, 10);
        $result = [];
        foreach ($owneds as $owned) {
            $result[] = [
                'id' => $owned->getId(),
                'user_id' => $owned->getUser(),
                'game_id' => $owned->getGame()
            ];
        }
        return $this->json([
            'result' => $result
        ]);
    }

    
}
