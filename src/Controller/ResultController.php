<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResultController extends AbstractController
{
    #[Route('/', name: 'app_result')]
    public function index(): Response
    {
        return $this->render('result/index.html.twig', [
            'controller_name' => 'ResultController'
        ]);
    }

    public function resultAjax(): Response
    {
        $data = [
            [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
            ],
            [
                'id' => 2,
                'name' => 'Jane Doe',
                'email' => 'jane.doe@example.com',
            ],
        ];

        return new JsonResponse(json_encode($data));
    }
}
