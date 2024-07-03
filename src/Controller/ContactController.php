<?php

namespace App\Controller;

use App\Form\ContactType; // Assurez-vous d'importer correctement ContactType
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail; // Importez correctement TemplatedEmail
use App\DTO\ContactDTO;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $data = new ContactDTO();
        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mail = (new TemplatedEmail())
                ->to('contact@demo.fr')
                ->from($data->email)
                ->subject('Demande de contact')
                ->htmlTemplate('emails/emails.html.twig')
                ->context(['data' => $data]);
            $mailer->send($mail);

            $this->addFlash('success', 'Votre message a bien été envoyé.');

           return $this->redirectToRoute('contact');
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
