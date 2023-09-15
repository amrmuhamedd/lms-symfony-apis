<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api', name: 'api_')]
class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user', methods: ['get'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/register', name: 'api_register', methods: ['POST'])]

    public function register(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        // Parse and validate the JSON request data
        $data = json_decode($request->getContent(), true);

        // Validate data (e.g., check for required fields)

        // Create a new User entity
        $user = new User();
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setRoles($data['roles']);

        // Hash the user's password
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $data['password']
        );
        $user->setPassword($hashedPassword);

        // Persist the user to the database
        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();


        $userData = $user->toResponseObject();
        // Return a success response
        return $this->json($userData, JsonResponse::HTTP_CREATED);
    }
}