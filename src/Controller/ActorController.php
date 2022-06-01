<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Actor;
use App\Form\ActorFormType;
use App\Form\ActorDeleteFormType;
use App\Service\FileService;

class ActorController extends AbstractController
{

    #[Route('/actor', name: 'app_actor')]
    public function index(): Response
    {
        return $this->render('actor/index.html.twig', [
            'controller_name' => 'ActorController',
        ]);
    }


    
    #[Route('/actores', name: 'actor_list')]
    public function list():Response
    {
        $actores = $this->getDoctrine()->getRepository(Actor::class)->findAll();
        
        return $this->render('actor/list.html.twig', [
            'actores' => $actores   
        ]);
    }
    

    #[Route('/actor/store', name: 'actor_create')]
    public function store(
        Request $request,
        FileService $fileService
        ):Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $actor = new Actor();
        $formulario = $this->createForm(ActorFormType::class, $actor);
        
        $formulario->handleRequest($request);
        
        if($formulario->isSubmitted() && $formulario->isValid()) {

            if($uploadedFile = $formulario->get('image')->getData()) {
                $fileService->setTargetDirectory($this->getParameter('app.actors.root'));
                $actor->setCaratula($fileService->upload($uploadedFile, true, 'actor_'));
            }
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($actor);
            $entityManager->flush();
            
            $this->addFlash('success', 'Actor guardado con id ' . $actor->getId());
            
            return $this->redirectToRoute('actor_show', ['id' => $actor->getId()]);
        }
                 
         return $this->render('actor/create.html.twig',
             ['formulario' => $formulario->createView()]);
    }

    #[Route('/actor/borrarFoto/{id}', name: 'actor_delete_image')]
    public function deleteImage(
        Request $request,
        FileService $fileService,
        Actor $actor
    ):Response {
        
        if($image = $actor->getImage()) {
            $fileService->setTargetDirectory($this->getParameter('app.actors.root'))
                ->remove($caratula);

            $actor->setImage(NULL);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($actor);
            $entityManager->flush();

            $this->addFlash('success', 'La imagen de ' . $pelicula->getNombre() . ' fue elminada.');
        }

            return $this->redirecttoRoute('actor_edit', [
                'id' => $actor->getId()
            ]);
    }
    

    
    #[Route('/actor/{id<\d+>}', name: 'actor_show')]
    public function show($id):Response {
        $actor = $this->getDoctrine()->getRepository(Actor::class)->find($id);
        
        if(!$actor)
            throw $this->createNotFoundException("No se encontró el actor $id");
        
            return $this->render('actor/detail.html.twig', ['actor' => $actor]);
    }
    

    
    #[Route('/actor/search/{campo}/{valor}', name: 'actor_search')]
    public function search($campo, $valor):Response {
        $criterio = [$campo=>$valor];
        $actor = $this->getDoctrine()->getRepository(Actor::class)->findBy($criterio);
        
        if(!$actor)
            throw $this->createNotFoundException("No se encontró el actor $id");
            
            return new Response("Lista de actores: <br>" .implode("<br>", $actor));
            
    }
    
   
    #[Route('/actor/edit/{id}', name: 'actor_edit')]
    public function edit(
        Actor $actor, 
        Request $request,
        FileService $fileService
        ):Response{

        $formulario = $this->createForm(ActorFormType::class, $actor);
        $imagenAntigua = $actor->getImage();
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario ->isValid()) {

            if($uploadedFile = $formulario->get('image')->getData()) {
                $fileService->setTargetDirectory($this->getParameter('app.actors.root'));

                $actor->setImage($fileService->replace (
                    $uploadedFile,
                    $imagenAntigua,
                    true,
                    'actor_'
                ));
            } else {
                $actor->setImage($imagenAntigua);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Actor actualizado correctamente.');

            return $this->redirectToRoute('actor_show', ['id'=> $actor->getId()]);
        }

        return $this->render("actor/edit.html.twig", [
            "formulario" => $formulario->createView(),
            "actor" => $actor
        ]);
    }
    

    #[Route('/actor/delete/{id}', name: 'actor_delete')]
    public function delete(
        Actor $actor, 
        Request $request
        ): Response{

        $formulario = $this->createForm(ActorDeleteFormType::class, $actor);
        $formulario->handleRequest($request);

        if($formulario->isSubmitted() && $formulario->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($actor);
            $entityManager->flush();

            if($image = $actor->getImage()) {
                $fileService->setTargetDirectory($this->getParameeter('app.cators.root'))
                            ->remove($image);
            }

            $this->addFlash('success', 'Actor eliminado correctamente.');

            return $this->redirectToRoute('actor_list');
        }

        return $this->render("actor/delete.html.twig", [
            "formulario" => $formulario->createView(),
            "actor" => $actor
        ]);
    }
    
    
}
