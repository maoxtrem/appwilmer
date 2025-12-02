<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/user', name: 'api_user_')]
#[OA\Tag(name: 'User')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly SerializerInterface $serializer
    ) {}

    // 📌 Listar todos los usuarios
    #[Route(name: 'list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of users',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class, groups: ['user:read']))
        )
    )]

    public function list(): JsonResponse
    {
        $users = $this->userRepository->findAll();
        $data = $this->serializer->serialize($users, 'json', ['groups' => 'user:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    // 📌 Mostrar un usuario por ID
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns a user',
        content: new Model(type: User::class, groups: ['user:read'])
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'The id of the user',
        schema: new OA\Schema(type: 'integer')
    )]
    public function show(User $user): JsonResponse
    {
        $data = $this->serializer->serialize($user, 'json', ['groups' => 'user:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    // 📌 Crear un nuevo usuario
    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\Response(
        response: 201,
        description: 'User created',
        content: new Model(type: User::class, groups: ['user:read'])
    )]
    #[OA\RequestBody(
        description: 'User object that needs to be added',
        required: true,
        content: new Model(type: User::class, groups: ['user:write'])
    )]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // ✅ Validación manual antes de serializar
        if (empty($data['username'])) {
            return new JsonResponse(['error' => 'Username is required'], Response::HTTP_BAD_REQUEST);
        }
        if (empty($data['password'])) {
            return new JsonResponse(['error' => 'Password is required'], Response::HTTP_BAD_REQUEST);
        }

        // ✅ Si pasa validación, deserializo al objeto
        $user = $this->serializer->deserialize(
            $request->getContent(),
            User::class,
            'json',
            ['groups' => 'user:write']
        );

        // Hash del password
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $data['password'])
        );

        $this->userRepository->save($user);

        $responseData = $this->serializer->serialize($user, 'json', ['groups' => 'user:read']);
        return new JsonResponse($responseData, Response::HTTP_CREATED, [], true);
    }

    // 📌 Actualizar un usuario existente
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[OA\Response(
        response: 200,
        description: 'User updated',
        content: new Model(type: User::class, groups: ['user:read'])
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'The id of the user',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        description: 'User object that needs to be updated',
        required: true,
        content: new Model(type: User::class, groups: ['user:write'])
    )]

    public function update(Request $request, User $user): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Deserializar sobre el objeto existente
        $this->serializer->deserialize($request->getContent(), User::class, 'json', [
            'object_to_populate' => $user,
            'groups' => 'user:write'
        ]);

        // Si viene password en el body, lo hashéo
        !empty($data['password']) && $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));
        $this->userRepository->save($user);

        return $this->json($user, Response::HTTP_OK, ['groups' => 'user:read']);
    }

    // 📌 Eliminar un usuario
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Response(
        response: 204,
        description: 'User deleted'
    )]
    #[OA\Response(
        response: 404,
        description: 'User not found'
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'The id of the user',
        schema: new OA\Schema(type: 'integer')
    )]
    public function delete(User $user): JsonResponse
    {
        $this->userRepository->remove($user);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
