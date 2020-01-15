<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\{ImageRepository, TrickRepository};
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    /**
     * @Route("/delete-image-{id}", name="delete_image", methods={"POST"})
     * @param Image           $image
     * @param Request         $request
     * @param TrickRepository $trickRepository
     *
     * @return JsonResponse
     */
    public function delete(Image $image, Request $request, TrickRepository $trickRepository)
    {
        $trick = $trickRepository->find($request->request->get('trick'));
        if (!$trick || !$image) {
            return new JsonResponse(['error' => true]);
        }
        $changed = false;
        // check token
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->request->get('token'))) {
            // replace main image of trick is this is the deleted one
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
     * @param Image           $image
     * @param Request         $request
     * @param TrickRepository $trickRepository
     *
     * @return JsonResponse
     */
    public function replaceMainImg(Image $image, Request $request, TrickRepository $trickRepository)
    {
        $trick = $trickRepository->find($request->request->get('trick'));
        // check token
        if ($trick && $this->isCsrfTokenValid('delete' . $image->getId(), $request->request->get('token'))) {
            $trick->setMainImage($image);
            $em = $this->getDoctrine()->getManager();
            $em->persist($trick);
            $em->flush();

            return new JsonResponse(['error' => false]);
        }

        return new JsonResponse(['error' => true]);
    }

    /**
     * @Route("/create-images", name="create_images")
     * @param Request                $request
     * @param ImageRepository        $imageRepository
     * @param TrickRepository        $trickRepository
     * @param EntityManagerInterface $entityManager
     *
     * @return JsonResponse
     */
    public function create(Request $request, ImageRepository $imageRepository, TrickRepository $trickRepository, EntityManagerInterface $entityManager)
    {
        $trick = $trickRepository->find($request->request->get('trick'));
        if (!$trick) {
            return new JsonResponse(['error' => true]);
        }

        $files   = $request->files->all();
        $images  = new ArrayCollection();
        $changed = null;

        foreach ($files as $file) {
            $image = new Image();
            $image->setImageFile($file)->setTrick($trick);
            if ($trick->getMainImage() === null) {
                // allow to update dynamically the background-image of edit form
                $changed = true;
            }
            $entityManager->persist($image);
            $images->add($image);
        }
        $entityManager->flush();
        $view = $this->renderView('parts/list-images.html.twig', ['images' => $images]);

        return new JsonResponse(['view' => $view, 'changed' => $changed ? $trick->getMainImage()->getImageName() : false]);
    }
}
