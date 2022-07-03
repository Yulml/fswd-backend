<?php

namespace App\Controller\Api;

use App\Repository\OwnedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class OwnedController extends AbstractController
{
    public function __construct(
        private OwnedRepository $ownedRepository
    ) {
    }

    #[Route('/api/owned', methods: 'GET')]
    public function index(Request $request): Response
    {
        // get all users, 10 per page
        return $this->json($this->userRepository->getAllUsersPaginated($request->query->get('page', 1), 10));
    }
    
    #[Route('/api/owned/delete/{id}', methods: 'DELETE')]
    public function delete($id, OwnedRepository $ownedRepository): Response
    {
        $owned = $ownedRepository->find($id);
        if ($owned == null) {
            throw $this->createNotFoundException('Key ' . $id . ' not found. There is no such owned game.');
        }

        try {
            $ownedRepository->remove($owned, true);
        } catch (\Exception $exception) {
            return $this->json([
                'message' => $exception->getMessage()
            ], 400);
        }
        return $this->json([204]);
    }

    
}
