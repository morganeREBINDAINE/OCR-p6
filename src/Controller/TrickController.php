<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\RegistrationType;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    /**
     * @Route("/figure-{id}", name="display_trick")
     */
    public function displayTrick(Trick $trick)
    {
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
        dump($form);
        if ($form->isSubmitted() && $form->isValid()) {
            $trickRepository->save($trick);
//            return $this->redirectToRoute('success');     redirect to new trick
        }

        return $this->render('tricks/form.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete-trick-{id}", name="delete_trick")
     */
    public function delete(Trick $trick, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($trick);
        $entityManager->flush();
        $this->addFlash('success', 'La figure a bien été supprimée.');
        return $this->redirectToRoute('success');
    }
}
