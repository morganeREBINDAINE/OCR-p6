<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    /**
     * @Route("/figure-{id}", name="display_trick")
     */
    public function displayTrick(Trick $trick, Request $request, EntityManagerInterface $entityManager, CommentRepository $commentRepository)
    {
        $comments = $commentRepository->findPaginated(10, 0, $trick);

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTrick($trick)->setUser($this->getUser());
            $entityManager->persist($comment);
            $entityManager->flush();
            $this->addFlash('success-comment', 'Votre commentaire a bien été ajouté.');
            return $this->redirectToRoute('display_trick', ['id' => $trick->getId()]);
        }

        return $this->render('tricks/single.html.twig', [
            'trick' => $trick,
            'comments' => $comments,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/ajax_request/comments", name="ajax_request.comments", methods={"POST"})
     * @param CommentRepository $commentRepository
     * @param TrickRepository   $trickRepository
     * @param Request           $request
     *
     * @return JsonResponse
     */
    public function ajaxRequestComments(CommentRepository $commentRepository, TrickRepository $trickRepository, Request $request) {
        $trick = $trickRepository->find($request->request->get('trick'));
        $comments = $commentRepository->findPaginated(5, $request->request->get('first'), $trick);
        $view = $this->renderView('parts/list-comments.html.twig', ['comments' => $comments]);
        $response = new JsonResponse(['view' => $view, 'count' => count($comments)]);

        return $response;
    }

    /**
     * @Route("/creer-figure", name="create_trick")
     */
    public function create(Request $request, TrickRepository $trickRepository, EntityManagerInterface $entityManager)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $videos = explode('|||', $request->request->get('videos'));

            foreach ($videos as $balise) {
                if (preg_match('#^<iframe.+>$#', $balise)) {
                    $video = new Video();
                    $video->setBalise($balise)->setTrick($trick);
                    $entityManager->persist($video);
                } else {
                    $this->addFlash('error', 'La balise '.$balise.' n\'est pas conforme.');
                }
            }

            try {
                $trickRepository->save($trick);
                $this->addFlash('success-create', 'Votre figure a bien été créée.');
            } catch(\Exception $e) {
                $this->addFlash('error-create', 'Il y a eu une erreur lors de l\'enregistrement de votre figure.') ;
            }
            return $this->redirectToRoute('home');
        }

        return $this->render('tricks/form.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/modifier-figure-{id}", name="edit_trick")
     * @param Trick           $trick
     * @param Request         $request
     * @param TrickRepository $trickRepository
     *
     * @return Response
     */
    public function edit(
        Trick $trick,
        Request $request,
        TrickRepository $trickRepository
    )
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickRepository->save($trick);
            $this->addFlash('success-edit', 'Votre figure a bien été modifiée.');
            return $this->redirectToRoute('display_trick', ['id' => $trick->getId()]);
        }

        return $this->render('tricks/form.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete-trick-{id}", name="delete_trick", methods={"POST"})
     */
    public function delete(Trick $trick, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            $em->remove($trick);
            $em->flush();
        }

        return $this->redirectToRoute('home');
    }
}
