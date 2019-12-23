<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(TrickRepository $trickRepository, UserRepository $userRepository)
    {
        $tricks = $trickRepository->findPaginatedTricks(10, 0);
        return $this->render('home/index.html.twig', [
            'tricks' => $tricks
        ]);
    }

    /**
     * @Route("/ajax_request/{first}", name="ajax_request")
     */
    public function ajaxRequest(TrickRepository $trickRepository, int $first) {
        $tricks = $trickRepository->findPaginatedTricks(10, $first);
        $view = $this->renderView('home/list-tricks.html.twig', ['tricks' => $tricks]);
        $response = new JsonResponse(['view' => $view]);

        return $response;
    }

    public function message() {
        return $this->render('home/message.html.twig');
    }

    /**
     * @Route("/succes", name="success")
     */
    public function success(Session $session) {
        return $session->getFlashBag()->has('success') ?
            $this->message():
            $this->redirectToRoute('home');
    }

    /**
     * @Route("/erreur", name="error")
     */
    public function error(Session $session) {
        return $session->getFlashBag()->has('error') ?
         $this->message():
         $this->redirectToRoute('home');
    }
}
