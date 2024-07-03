<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Form\PostType;
use App\Form\CommentType;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Knp\Component\Pager\PaginatorInterface;

class PostController extends AbstractController
{
    #[Route('/post', name: 'post.index', methods: ['GET'])]
    public function index(PostRepository $postRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $postRepository->createQueryBuilder('p')
            ->andWhere('p.Status = :status')
            ->setParameter('status', true);

        $posts = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/post/new', name: 'post.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $author = $this->getUser();
        if (!$this->getUser()) {
            throw new AccessDeniedException('Vous devez être connecté pour créer un post.');
        }

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setAuthor($this->getUser());

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/post/{id}', name: 'post.show', methods: ['GET', 'POST'])]
    public function show(Request $request, Post $post, CommentRepository $commentRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$post->isStatus()) {
            throw $this->createNotFoundException('Le post demandé n\'existe pas ou n\'est pas publié.');
        }

        $comments = $commentRepository->findByPostWithStatus($post->getId());

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if (!$user) {
                throw new AccessDeniedException('Vous devez être connecté pour commenter.');
            }
            $comment->setAuthor($user);
            $comment->setPost($post);
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setStatus(true); // Par défaut, le commentaire est approuvé

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('post.show', ['id' => $post->getId()]);
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'commentForm' => $form->createView(),
        ]);
    }

    #[Route('/post/{id}/edit', name: 'post.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('post.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/post/{id}', name: 'post.delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->get('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post.index', [], Response::HTTP_SEE_OTHER);
    }

}
