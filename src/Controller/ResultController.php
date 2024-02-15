<?php

namespace App\Controller;

use App\Repository\Voting\VoteResultRepository;
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

    public function resultAjax(VoteResultRepository $repository): Response
    {
        $data = $repository->fetchData();
        return new JsonResponse(json_encode($data));
    }
}
