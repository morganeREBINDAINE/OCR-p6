<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $view = $this->renderView('home/list-tricks.html.twig', ['tricks' => $tricks]);
        $response = new JsonResponse(['view' => $view]);

        return $response;
    }
}
