<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ListSouvenirsController extends AbstractController
{
    #[Route('/list/souvenirs', name: 'app_list_souvenirs')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ListSouvenirsController.php',
        ]);
    }
}
