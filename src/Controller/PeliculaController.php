<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Pelicula;

class PeliculaController extends AbstractController
{
    #[Route('/', name: 'app_pelicula')]
    public function index():Response
    {
        $pelis = $this->getDoctrine()->getRepository(Pelicula::class)->findAll();
        
        return new Response("Llista de pelis: <br> ". implode("<br>", $pelis));
    }
    
    #[Route('/pelicula/store', name: 'pelicula_store')]
    public function store():Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        
        $peli = new Pelicula();
        $peli->setTitulo('Germà Ós')
            ->setDuracion(130)
            ->setDirector('Nose')
            ->setGenero('Animació');
        
         $entityManager->persist($peli);
         $entityManager->flush();
         
         return new Response('Película guardada con id ' . $peli->getId());
    }
    
    
    #[Route('/pelicula/{id}', name: 'pelicula_show')]
    public function show($id):Response {
        $peli = $this->getDoctrine()->getRepository(Pelicula::class)->find($id);
        
        if(!$peli)
            throw $this->createNotFoundException("No se encontró la peli $id");
        
            return new Response("Información de la película: $peli");
        
    }
    
    
    #[Route('/pelicula/search/{campo}/{valor}', name: 'pelicula_search')]
    public function search($campo, $valor):Response {
        $criterio = [$campo=>$valor];
        $peli = $this->getDoctrine()->getRepository(Pelicula::class)->findBy($criterio);
        
        if(!$peli)
            throw $this->createNotFoundException("No se encontró la peli $id");
            
            return new Response("Lista de pelis: <br>" .implode("<br>", $peli));
            
    }
    
    #[Route('/pelicula/update/{id}', name: 'pelicula_update')]
    public function update($id):Response 
    {
        $entityManager = $this->getDoctrine()->getManager();
        $peli = $entityManager->getRepository(Pelicula::class)->find($id);
        
        if(!$peli)
            throw $this->createNotFoundException("No se encontró la peli $id");
            
       $peli->setTitulo("Terminator II - Judgment Day");
       
       $entityManager->flush();
       
       return $this->redirectToRoute('pelicula_show', ['id' => $id]);
            
    }
    
    
    #[Route('/pelicula/destroy/{id}', name: 'pelicula_destroy')]
    public function destroy($id):Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $peli = $entityManager->getRepository(Pelicula::class)->find($id);
        
        if(!$peli)
            throw $this->createNotFoundException("No se encontró la peli $id");
            
            $entityManager->remove($peli);
            $entityManager->flush();
            
            return new Response("La película <b>$peli</b> fue eliminada correctamente.");
            
    }
}
    
