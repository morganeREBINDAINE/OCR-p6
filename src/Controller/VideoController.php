<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends AbstractController
{
    /**
     * @Route("/delete-video-{id}", name="delete_video")
     */
    public function delete(Image $image, Request $request, TrickRepository $trickRepository)
    {
//        dump($request->request->all(), $image, $this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('token')));
//        $trick = $trickRepository->find($request->request->get('trick'));
//        $changed = false;
//
//
//        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('token'))) {
//            if ($trick->getMainImage() === $image) {
//                $images = $trick->getImages();
//                $images->removeElement($image);
//                $images->isEmpty() ? $trick->setMainImage(null) : $trick->setMainImage($images->first());
//                $changed = $trick->getMainImage() ? $trick->getMainImage()->getImageName() : 'empty';
//            }
//            $em = $this->getDoctrine()->getManager();
//            $em->remove($image);
//            $em->flush();
//        }
//        return new JsonResponse(['changed' => $changed]);
    }

    /**
     * @Route("/create_video", name="create_video")
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

        $view = $this->renderView('tricks/list-videos.html.twig', ['videos' => [$video]]);

        return new JsonResponse(['view' => $view]);
    }
}
