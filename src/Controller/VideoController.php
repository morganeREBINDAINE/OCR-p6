<?php

namespace App\Controller;

use App\Entity\Video;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends AbstractController
{
    /**
     * @Route("/delete-video-{id}", name="delete_video", methods={"POST"})
     */
    public function delete(Video $video, Request $request)
    {
        if ($this->isCsrfTokenValid('delete'.$video->getId(), $request->request->get('token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($video);
            $em->flush();
        }

        return new JsonResponse(['success' => true]);
    }

    /**
     * @Route("/create-video", name="create_video")
     */
    public function create(Request $request, TrickRepository $trickRepository, EntityManagerInterface $entityManager)
    {
        $trick = $trickRepository->find($request->request->get('trick'));
        $iframe = $request->request->get('iframe');

        $video = new Video();
        $video->setTrick($trick)->setBalise($iframe);
        $entityManager->persist($video);
        $trick->addVideo($video);
        $entityManager->flush();

        $view = $this->renderView('parts/list-videos.html.twig', ['videos' => [$video]]);

        return new JsonResponse(['view' => $view]);
    }
}
