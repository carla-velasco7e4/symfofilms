<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Pelicula;
use App\Form\PeliculaFormType;
use App\Form\PeliculaDeleteFormType;
use Psr\Log\LoggerInterface;
use App\Service\FileService;

class PeliculaController extends AbstractController
{
    
    #[Route('/', name: 'portada')]
    public function index():Response
    {      
        return $this->render('pelicula/index.html.twig');
    }
    

    #[Route('/peliculas', name: 'pelicula_list')]
    public function list():Response
    {
        $pelis = $this->getDoctrine()->getRepository(Pelicula::class)->findAll();
        
        return $this->render('pelicula/list.html.twig', [
            'peliculas' => $pelis   
        ]);
    }
    

    #[Route('/pelicula/store', name: 'pelicula_create', methods: ["GET", "POST"])]
    public function store(
        Request $request, 
        LoggerInterface $appInfoLogger,
        FileService $fileService):Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $peli = new Pelicula();

        $formulario = $this->createForm(PeliculaFormType::class, $peli);
        $formulario->handleRequest($request);

        
        if($formulario->isSubmitted() && $formulario->isValid()) {

            if($uploadedFile = $formulario->get('image')->getData()) {
                $fileService->setTargetDirectory($this->getParameter('app.covers.root'));
                $peli->setCaratula($fileService->upload($uploadedFile, true, 'cover_'));
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($peli);
            $entityManager->flush();
            
            $mensaje = 'Película ' . $peli->getTitulo(). ' guardada con id ' . $peli->getId();
            $this->addFlash('success', 'Película guardada con id ' . $peli->getId());
            $appInfoLogger->info($mensaje);
            
            return $this->redirectToRoute('pelicula_show', ['id' => $peli->getId()]);
        }
                 
         return $this->render('pelicula/create.html.twig',
             ['formulario' => $formulario->createView()]);
    }


    #[Route('/pelicula/borrarCaratula/{id}', name: 'pelicula_delete_cover')]
    public function deleteCover(
        Request $request,
        FileService $fileService,
        Pelicula $pelicula
    ):Response {
        
        if($caratula = $pelicula->getCaratula()) {
            $fileService->setTargetDirectory($this->getParameter('app.covers.root'))
                        ->remove($caratula);

            $pelicula->setCaratula(NULL);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pelicula);
            $entityManager->flush();

            $this->addFlash('success', 'La carátula de ' . $pelicula->getTitulo() . ' fue borrada.');
        }
        
            return $this->redirectToRoute('pelicula_edit', [
                'id' => $pelicula->getId()
                ]);
    }
    
  

    
    #[Route('/pelicula/{id<\d+>}', name: 'pelicula_show')]
    public function show($id):Response {
        $peli = $this->getDoctrine()->getRepository(Pelicula::class)->find($id);
        
        if(!$peli)
            throw $this->createNotFoundException("No se encontró la peli $id");
        
            return $this->render('pelicula/detail.html.twig', ['pelicula' => $peli]);
    }
    

    
    #[Route('/pelicula/search/{campo}/{valor}', name: 'pelicula_search')]
    public function search($campo, $valor):Response {
        $criterio = [$campo=>$valor];
        $peli = $this->getDoctrine()->getRepository(Pelicula::class)->findBy($criterio);
        
        if(!$peli)
            throw $this->createNotFoundException("No se encontró la peli $id");
            
            return new Response("Lista de pelis: <br>" .implode("<br>", $peli));
            
    }
    
   
    #[Route('/pelicula/edit/{id}', name: 'pelicula_edit')]
    public function edit(
            Pelicula $peli, 
            Request $request,
            FileService $fileService
        ):Response{

        $formulario = $this->createForm(PeliculaFormType::class, $peli);
        $caratulaAntigua = $peli->getCaratula();
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario ->isValid()) {

            if($uploadedFile = $formulario->get('image')->getData()) {

            $fileService->setTargetDirectory($this->getParameter('app.covers.root'));

            $peli->setCaratula($fileService->replace (
                $uploadedFile,
                $caratulaAntigua,
                true,
                'cover_'
            ));
        } else {
            $peli->setCaratula($caratulaAntigua);
        }


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Película actualizada correctamente.');

            return $this->redirectToRoute('pelicula_show', ['id'=> $peli->getId()]);
        }


        return $this->render("pelicula/edit.html.twig", [
            "formulario" => $formulario->createView(),
            "pelicula" => $peli
        ]);
    }
    

    #[Route('/pelicula/delete/{id}', name: 'pelicula_delete')]
    public function delete(Pelicula $peli, Request $request): Response{

        $formulario = $this->createForm(PeliculaDeleteFormType::class, $peli);
        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($peli);
            $entityManager->flush();
            
            if($caratula = $peli->getCaratula()) {
                $fileService->setTargetDirectory($this->getParameter('app.covers.root'))
                            ->remove($caratula);
            }

            $this->addFlash('success', 'Película eliminada correctamente.');

            return $this->redirectToRoute('pelicula_list');
        }

        return $this->render("pelicula/delete.html.twig", [
            "formulario" => $formulario->createView(),
            "pelicula" => $peli
        ]);
    }
    
    
}
    
