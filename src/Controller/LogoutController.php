<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LogoutController extends AbstractController
{
    #[Route('/logout', name: 'logout')]
    public function index(): Response
    {
        return $this->render('logout/logout.html.twig', [
            'controller_name' => 'LogoutController',
        ]);
    }
}
