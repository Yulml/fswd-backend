<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    #[Route('/api/user/{user}/game/get', methods: 'GET')]
    public function getGamesAction(User $user): Response
    {
        return $this->json([
            'result' => $this->userRepository->getUserGames($user)
        ]);
    }

    #[Route('/api/user', methods: 'GET')]
    public function index(Request $request): Response
    {
        // get all users, 10 per page
        return $this->json($this->userRepository->getAllUsersPaginated($request->query->get('page', 1), 10));
    }

    #[Route('/api/user/new', methods: 'POST')]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        // create new user
        $validator = ['email', 'password', 'roles', 'nickname', 'dateofbirth', 'avatar'];
        $data = $request->toArray();

        foreach ($validator as $validation) {
            if (!isset($data[$validation])) {
                return new Response('key ' . $validation . ' not', 422); // 422: entity not processable
            }
        }
        $user = $userRepository->createUser($data);
        $info = ["status"=> "succeeded", "data"=> $user->toArray(),"error"=>null];
        return $this->json($info);
    }

    #[Route('/api/user/{id}', methods: 'GET')]
    public function show($id, UserRepository $userRepository): Response
    {
        // show specific user
        $user = $userRepository->find($id);
        if ($user == null) {
            throw $this->createNotFoundException();
        }
        return $this->json($user->toArray());
    }
    
    #[Route('/api/user/{id}/collector', methods: 'GET')]
    public function getCollectorAction(User $user): Response
    {
        //show only nick and avatar specific user
        return $this->json($this->userRepository->getCollector($user));
    }

    #[Route('/api/user/edit/{user}', methods: 'PUT')]
    public function edit(Request $request, User $user): Response
    {
        return $this->json($this->userRepository->updateUser($user, $request->toArray())->toArray());
    }

    #[Route('/api/user/delete/{id}', methods: 'DELETE')]
    public function delete($id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        if ($user == null) {
            throw $this->createNotFoundException('key' . $id . 'not found. There is no such user.');
        }
        if (!$this->isGranted('ROLE_SUPERADMIN')) {
            throw $this->createAccessDeniedException('Only a superadmin can delete users.');
        }

        try {
            $userRepository->remove($user, true);
        } catch (\Exception $exception) {
            return $this->json([
                'message' => $exception->getMessage()
            ], 400);
        }

        // We return it here (although it shouldnt have an id anymore) just in case we want to see some info
        // right after deleting the user.
        return $this->json($user->toArray());
    }
}
