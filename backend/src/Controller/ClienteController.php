<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Repository\ClienteRepository;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/cliente', name: 'api_cliente_')]
#[OA\Tag(name: 'Cliente')]
final class ClienteController extends AbstractController
{
    public function __construct(
        private readonly ClienteRepository $clienteRepository,
        private readonly SerializerInterface $serializer
    ) {}

    // 📌 Listar todos los clientes
    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of clientes',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Cliente::class, groups: ['cliente:read']))
        )
    )]
    public function index(): JsonResponse
    {
        $clientes = $this->clienteRepository->findAll();
        $data = $this->serializer->serialize($clientes, 'json', ['groups' => 'cliente:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    // 📌 Mostrar un cliente por ID
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns a cliente',
        content: new Model(type: Cliente::class, groups: ['cliente:read'])
    )]
    #[OA\Response(response: 404, description: 'Cliente not found')]
    #[OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'integer'))]
    public function show(Cliente $cliente): JsonResponse
    {
        $data = $this->serializer->serialize($cliente, 'json', ['groups' => 'cliente:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    // 📌 Crear un nuevo cliente
    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\Response(
        response: 201,
        description: 'Cliente created',
        content: new Model(type: Cliente::class, groups: ['cliente:read'])
    )]
    #[OA\RequestBody(
        description: 'Cliente object that needs to be added',
        required: true,
        content: new Model(type: Cliente::class, groups: ['cliente:write'])
    )]
    public function create(Request $request): JsonResponse
    {
        $cliente = $this->serializer->deserialize(
            $request->getContent(),
            Cliente::class,
            'json',
            ['groups' => 'cliente:write']
        );

        $this->clienteRepository->save($cliente);

        $data = $this->serializer->serialize($cliente, 'json', ['groups' => 'cliente:read']);
        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    // 📌 Actualizar un cliente existente
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[OA\Response(
        response: 200,
        description: 'Cliente updated',
        content: new Model(type: Cliente::class, groups: ['cliente:read'])
    )]
    #[OA\Response(response: 404, description: 'Cliente not found')]
    #[OA\RequestBody(
        description: 'Cliente object that needs to be updated',
        required: true,
        content: new Model(type: Cliente::class, groups: ['cliente:write'])
    )]
    public function update(Request $request, Cliente $cliente): JsonResponse
    {
        $this->serializer->deserialize(
            $request->getContent(),
            Cliente::class,
            'json',
            ['object_to_populate' => $cliente, 'groups' => 'cliente:write']
        );

        $this->clienteRepository->save($cliente);

        return $this->json($cliente, Response::HTTP_OK, ['groups' => 'cliente:read']);
    }

    // 📌 Eliminar un cliente
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Response(response: 204, description: 'Cliente deleted')]
    #[OA\Response(response: 404, description: 'Cliente not found')]
    public function delete(Cliente $cliente): JsonResponse
    {
        $this->clienteRepository->remove($cliente);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
