<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;


#[Route('/api', name: 'app_')]
final class ApiController extends AbstractController
{

    #[Route(name: 'api')]
    public function index(Request $request): JsonResponse
    {
        return new JsonResponse(['status' => 'ok']);
    }


}
