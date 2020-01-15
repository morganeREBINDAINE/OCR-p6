<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    /**
     * @Route("/delete-image-{id}", name="delete_image", methods={"POST"})
     */
    public function delete(Image $image, Request $request, TrickRepository $trickRepository)
    {
        $trick = $trickRepository->find($request->request->get('trick'));

        if (!$trick || !$image) {
            return new JsonResponse(['error' => true]);
        }

        $changed = false;

        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('token'))) {
            if ($trick->getMainImage() === $image) {
                $images = $trick->getImages();
                $images->removeElement($image);
                $images->isEmpty() ? $trick->setMainImage(null) : $trick->setMainImage($images->first());
                $changed = $trick->getMainImage() ? $trick->getMainImage()->getImageName() : 'empty';
            }
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();
        }

        return new JsonResponse(['changed' => $changed]);
    }

    /**
     * @Route("/replace-mainimg-{id}", name="replace_mainimage")
     */
    public function replaceMainImg(Image $image, Request $request, TrickRepository $trickRepository)
    {
        $trick = $trickRepository->find($request->request->get('trick'));

        if ($trick && $this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('token'))) {
            $trick->setMainImage($image);
            $em = $this->getDoctrine()->getManager();
            $em->persist($trick);
            $em->flush();
            return new JsonResponse(['error' => false]);
        }
        return new JsonResponse(['error' => true]);
    }

    /**
     * @Route("/create_images", name="create_images")
     */
    public function create(Request $request, ImageRepository $imageRepository, TrickRepository $trickRepository, EntityManagerInterface $entityManager)
    {
        $trick = $trickRepository->find($request->request->get('trick'));

        if (!$trick) {
            return new JsonResponse(['error' => true]);
        }

        $files = $request->files->all();
        $images = new ArrayCollection();
        $changed = false;

        foreach ($files as $file) {
            $image = new Image();
            $image->setImageFile($file)->setTrick($trick);
            if($trick->getMainImage() === null) {
                $changed = $image->getImageName();
            }
            $entityManager->persist($image);
            $images->add($image);
        }

        try {
            $entityManager->flush();
            $view = $this->renderView('parts/list-images.html.twig', ['images' => $images]);
            return new JsonResponse(['view' => $view, 'changed' => $changed]);
        } catch(\Exception $e) {
            return new JsonResponse(['error' => true]);
        }

    }
}
