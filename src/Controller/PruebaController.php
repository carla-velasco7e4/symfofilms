<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Pelicula;

class PruebaController extends AbstractController
{

    #[Route('/prueba', name: 'prueba_list')]
    public function list(): Response
    {

        $pelis = $this->getDoctrine()->getRepository(Pelicula::class)->findAll();

        return $this->render('prueba/index.html.twig', [
            'peliculas' => $pelis,
        ]);
    }
}
