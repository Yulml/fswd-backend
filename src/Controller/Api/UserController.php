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

    #[Route('/api/user/', methods: 'POST')]
    public function new(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        // add new user
        $data = $request->toArray();
        if (isset($data['email'])) {
            if (isset($data['password'])) {
                if (isset($data['roles'])) {
                    if (isset($data['nickname'])) {
                        if (isset($data['dateofbirth'])) {
                            if (isset($data['avatar'])) {
                                $user = new User();
                                $user->setEmail($data['email']);

                                $hashedPassword = $passwordHasher->hashPassword(
                                    $user,
                                    $data['password']
                                );
                                $user->setPassword($hashedPassword);

                                $user->setRoles($data['roles']);
                                $user->setNickname($data['nickname']);
                                $user->setDob(new \DateTime($data['dateofbirth']), 'Y/m/d');
                                $user->setAvatar($data['avatar']);
                                $em->persist($user);
                                $em->flush();
                                return $this->json([
                                    'id' => $user->getId(),
                                    'email' => $user->getEmail(),
                                    'password' => $user->getPassword(),
                                    'roles' => $user->getRoles(),
                                    'nickname' => $user->getNickname(),
                                    'dateofbirth' => $user->getDob(),
                                    'avatar' => $user->getAvatar(),
                                ]);
                            } else {
                                return new Response('Avatar field not found', 400);
                            }
                        } else {
                            return new Response('Date of birth field not found', 400);
                        }
                    } else {
                        return new Response('Nickname field not found', 400);
                    }
                } else {
                    return new Response('Roles field not found', 400);
                }
            } else {
                return new Response('Password field not found', 400);
            }
        } else {
            return new Response('Email field not found', 400);
        }
    }

    #[Route('/api/user/{id}', methods: 'GET')]
    public function show($id, UserRepository $userRepository): Response
    {
        // show specific user
        $user = $userRepository->find($id);
        if ($user == null) {
            throw $this->createNotFoundException();
        }
        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'roles' => $user->getRoles(),
            'nickname' => $user->getNickname(),
            'dateofbirth' => $user->getDob(),
            'avatar' => $user->getAvatar(),
        ]);
    }

    #[Route('/api/user/{id}', methods: 'PUT')]
    public function edit(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        // edit user
        $result = 'ok PUT';
        return $this->json([
            'users' => $result
        ]);
    }

    #[Route('/api/user/{id}', methods: 'DELETE')]
    public function delete(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        // delete user
        $result = 'ok DELETE';
        return $this->json([
            'users' => $result
        ]);
    }
}
