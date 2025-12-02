<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/user',name: 'app_user_')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly SerializerInterface $serializer
    ) {}

    // 📌 Listar todos los usuarios
    #[Route(name: 'list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $users = $this->userRepository->findAll();
        $data = $this->serializer->serialize($users, 'json', ['groups' => 'user:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    // 📌 Mostrar un usuario por ID
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user): JsonResponse
    {
        $data = $this->serializer->serialize($user, 'json', ['groups' => 'user:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    // 📌 Crear un nuevo usuario
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        // Hash del password antes de guardar
        if (!empty($user->getPassword())) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
        }
        $this->userRepository->save($user);
        $data = $this->serializer->serialize($user, 'json', ['groups' => 'user:read']);
        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    // 📌 Actualizar un usuario existente
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, User $user): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Deserializar sobre el objeto existente
        $this->serializer->deserialize($request->getContent(), User::class, 'json', [
            'object_to_populate' => $user,
        ]);

        // Si viene password en el body, lo hashéo
        if (!empty($data['password'])) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));
        }

        $this->userRepository->save($user);

        return $this->json($user, Response::HTTP_OK, ['groups' => 'user:read']);
    }

    // 📌 Eliminar un usuario
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(User $user): JsonResponse
    {
        $this->userRepository->remove($user);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
