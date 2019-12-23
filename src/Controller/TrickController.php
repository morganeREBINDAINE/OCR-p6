<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\RegistrationType;
use App\Form\TrickType;
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
    public function create(Request $request)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);
        dump($form);
        if ($form->isSubmitted() && $form->isValid()) {
//            return $this->redirectToRoute('success');     redirect to new trick
        }

        return $this->render('tricks/form.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }
}
