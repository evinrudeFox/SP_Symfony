<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(PostRepository $postRepository): Response
    {
        // Récupérer les trois derniers posts
        $latestPosts = $postRepository->findBy([], ['createdAt' => 'DESC'], 3);

        return $this->render('home/home.html.twig', [
            'latestPosts' => $latestPosts,
        ]);
    }
}


