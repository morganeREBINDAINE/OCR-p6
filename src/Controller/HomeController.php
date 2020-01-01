<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use App\Services\Mailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(TrickRepository $trickRepository)
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
        $view = $this->renderView('parts/list-tricks.html.twig', ['tricks' => $tricks]);
        $response = new JsonResponse(['view' => $view]);

        return $response;
    }

    /**
     * @Route("/succes", name="success")
     * @Route("/erreur", name="error")
     */
    public function message(Session $session) {
        return ($session->getFlashBag()->has('success') || $session->getFlashBag()->has('error')) ?
            $this->render('home/message.html.twig'):
            $this->redirectToRoute('home');
    }
}
