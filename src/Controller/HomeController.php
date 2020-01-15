<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param TrickRepository $trickRepository
     *
     * @return Response
     */
    public function index(TrickRepository $trickRepository)
    {
        $tricks = $trickRepository->findPaginated(10, 0);
        return $this->render('home/index.html.twig', ['tricks' => $tricks]);
    }

    /**
     * @Route("/ajax_request/tricks", name="ajax_request.tricks", methods={"POST"})
     */
    public function ajaxRequestTricks(TrickRepository $trickRepository, Request $request) {
        $tricks = $trickRepository->findPaginated(10, $request->request->get('first'));
        $view = $this->renderView('parts/list-tricks.html.twig', ['tricks' => $tricks]);
        $response = new JsonResponse(['view' => $view, 'count' => count($tricks)]);
        return $response;
    }
}
