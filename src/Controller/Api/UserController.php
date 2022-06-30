<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{

    #[Route('/api/user/{user}/game/get', methods: 'GET')]
    public function getGamesAction(User $user, UserRepository $userRepository): Response
    {
        return $this->json([
            'result' => $userRepository->getUserGames($user)
        ]);
    }


    #[Route('/api/user', methods: 'GET')]
    public function index(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        // show full list of users
        $currentPage = $request->query->get('page', 1);
        $query = $userRepository->getQueryAll();
        $users = $paginator->paginate($query, $currentPage, 10);
        $result = [];
        foreach ($users as $user) {
            $result[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'roles' => $user->getRoles(),
                'nickname' => $user->getNickname(),
                'dateofbirth' => $user->getDob(),
                'avatar' => $user->getAvatar(),
            ];
        }
        return $this->json([
            'result' => $result
        ]);
    }

    #[Route('/api/user/new', methods: 'POST')]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $validator = ['email', 'password', 'roles', 'nickname', 'dateofbirth', 'avatar'];
        $data = $request->toArray();

        foreach ($validator as $validation) {
            if (!isset($data[$validation])) {
                return new Response('key ' . $validation . ' not', 422); // 422: entity not processable
            }
        }

        $user = $userRepository->createUser($data);

        return $this->json($user->toArray());
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

    #[Route('/api/user/edit/{id}', methods: 'PUT')]
    public function edit(Request $request, $id, UserRepository $userRepository, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        // edit user
        $data = $request->toArray();
        $user = $userRepository->find($id);

        if ($user == null) {
            throw $this->createNotFoundException('key' . $id . 'not found. There is no such user.');
        }
        if ($data['email'] !== '') {
            $user->setEmail($data['email']);
        }
        if ($data['roles'] !== '' || $data['roles'] !== 'ROLE_USER') {
            $user->setRoles($data['roles']);
        }
        if ($data['password'] !== '') {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);
        }
        if ($data['nickname'] !== '') {
            $user->setNickname($data['nickname']);
        }
        if ($data['dateofbirth'] !== '') {
            $user->setDob(new \DateTime($data['dateofbirth']), 'Y/m/d');
        }
        if ($data['avatar'] !== '') {
            $user->setAvatar($data['avatar']);
        }
        $em->flush();
        return $this->json($user->toArray());
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
