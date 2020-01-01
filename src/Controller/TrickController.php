<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Video;
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
    public function create(Request $request, TrickRepository $trickRepository, EntityManagerInterface $entityManager)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $videos = explode('|||', $request->request->get('videos'));

            foreach ($videos as $balise) {
                $video = new Video();
                $video->setBalise($balise)->setTrick($trick);
                $entityManager->persist($video);
            }

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
        TrickRepository $trickRepository
    )
    {
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
     * @Route("/delete-trick-{id}", name="delete_trick", methods={"DELETE"})
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
