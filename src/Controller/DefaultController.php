<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ContactFormType;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class DeafaultController extends AbstractController
{
    #[Route('/contact', name: 'contacto')]
    public function index(
        Request $request,
        MailerInterface $mailer
    ): Response {

        $formulario = $this->createForm(ContactFormType::class);
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            $datos = $formulario->getData();

            $email = new TemplatedEmail();
            $email
                ->from(new Address($datos['email'], $datos['nombre']))
                ->to($this->getParameter('app.admin_email'))
                ->subject($datos['asunto'])
                ->htmlTemplate('email/contact.html.twig')
                ->context([
                    'de' => $datos['email'],
                    'nombre' => $datos['nombre'],
                    'asunto' => $datos['asunto'],
                    'mensaje' => $datos['mensaje']
                ]);

            $mailer->send($email);

            $this->addFlash('success', 'Mensaje enviado correctamente.');
            return $this->redirectToRoute('portada');
        }

        return $this->renderForm('contact.html.twig', [
            'formulario' => $formulario,
            'contact' => "Contacto con plantilla"
        ]);
    }


    #[Route('/contact2', name: 'contacto2')]
    public function index2(
        Request $request,
        MailerInterface $mailer
    ): Response {

        $formulario = $this->createForm(ContactFormType::class);
        $formulario->handleRequest($request);

        if ($formulario->isSubmitted() && $formulario->isValid()) {

            $datos = $formulario->getData();

            $email = new Email();
            $email
                ->from($datos['email'])
                ->to($this->getParameter('app.admin_email'))
                ->subject($datos['asunto'])
                ->text($datos['mensaje']);

            $mailer->send($email);

            $this->addFlash('success', 'Mensaje enviado correctamente.');
            return $this->redirectToRoute('portada');
        }

        return $this->renderForm('contact.html.twig', [
            'formulario' => $formulario,
            'contact' => "Contacto normal"
        ]);
    }


    #[Route('/novedades', name: 'novedades')]
    public function novedades(
        Request $request,
    ): Response {

        /* $actores = */
       /*  $peliculas =  */

        return $this->renderForm('contact.html.twig', [
            /* 'formulario' => $formulario, */
            'contact' => "Contacto normal"
        ]);
    }
}
