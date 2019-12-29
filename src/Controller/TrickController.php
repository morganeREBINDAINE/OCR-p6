<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    /**
     * @Route("/figure-{id}", name="display_trick")
     */
    public function displayTrick(Trick $trick)
    {
        dump($trick);
        return $this->render('tricks/single.html.twig', [
            'trick' => $trick
        ]);
    }

    /**
     * @Route("/creer-figure", name="create_trick")
     */
    public function create(Request $request, TrickRepository $trickRepository)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickRepository->save($trick);
            $this->addFlash('success', 'Votre figure a bien été créée. La voici !');
            return $this->redirectToRoute('display_trick', ['id' => $trick->getId()]);
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
        TrickRepository $trickRepository,
        ImageRepository $imageRepository
    )
    {
        dump($trick->getVideos()->first());
//        $image = $imageRepository->find(6);
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickRepository->save($trick);
            $this->addFlash('success', 'Votre figure a bien été modifiée.');
            return $this->redirectToRoute('display_trick', ['id' => $trick->getId()]);
        }

        return $this->render('tricks/form.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete-trick-{id}", name="delete_trick")
     */
    public function delete(Trick $trick, Request $request)
    {
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($trick);
            $em->flush();
        }

        return $this->redirectToRoute('home');
    }
}
