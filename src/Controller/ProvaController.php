<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Pelicula;

class ProvaController extends AbstractController
{
    #[Route('/prova', name: 'app_prova')]
    public function index(): Response
    {
        return $this->render('prova/index.html.twig', [
            'controller_name' => 'ProvaController',
        ]);
    }
}
