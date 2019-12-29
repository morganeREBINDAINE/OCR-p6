<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    /**
     * @Route("/delete-image-{id}", name="delete_image")
     */
    public function delete(Image $image, Request $request)
    {
        $trick = $image->getTrick();
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();
        }

        return $this->redirectToRoute('edit_trick', ['id' => $trick->getId()]);
    }

    /**
     * @Route("/create_images", name="create_images")
     */
    public function create(Request $request, ImageRepository $imageRepository, TrickRepository $trickRepository)
    {
        $trick = $trickRepository->find($request->request->get('trick'));
        $files = $request->files->all();
        $images = new ArrayCollection();

        foreach ($files as $file) {
            $image = new Image();
            $image->setImageFile($file)->setTrick($trick);
            $imageRepository->save($image);
            $images->add($image);
        }

        $view = $this->renderView('tricks/list-images.html.twig', ['images' => $images]);
        return new JsonResponse(['view' => $view]);
    }
}
