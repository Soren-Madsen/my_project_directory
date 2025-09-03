<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController{

    #[Route('/')]
    public function index(): Response
    {
        return $this->render('index.html.twig', ["name" => "Sóren"]);
    }

    #[Route('/my_project_directory')]
    public function test(): Response
    {
        return new JsonResponse(["id" => 1234]);
    }
}