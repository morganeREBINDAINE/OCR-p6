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
     * @Route("/delete-image-{id}", name="delete_image")
     */
    public function delete(Image $image, Request $request, TrickRepository $trickRepository)
    {
        dump($request->request->all(), $image, $this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('token')));
        $trick = $trickRepository->find($request->request->get('trick'));
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

        // @todo send error

        return new JsonResponse(['changed' => $changed]);
    }

    /**
     * @Route("/replace-mainimg-{id}", name="replace_mainimage")
     */
    public function replaceMainImg(Image $image, Request $request, TrickRepository $trickRepository)
    {
        $result = 'error';
        $trick = $trickRepository->find($request->request->get('trick'));

        if ($trick && $this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('token'))) {
            $trick->setMainImage($image);
            $em = $this->getDoctrine()->getManager();
            $em->persist($trick);
            $em->flush();
            $result = 'success';
        }
        // @todo if not trick or image send error
        return new JsonResponse(['result' => $result]);
    }

    /**
     * @Route("/create_images", name="create_images")
     */
    public function create(Request $request, ImageRepository $imageRepository, TrickRepository $trickRepository, EntityManagerInterface $entityManager)
    {
        $trick = $trickRepository->find($request->request->get('trick'));
        // @todo if not trick
        $files = $request->files->all();
        $images = new ArrayCollection();
        $changed = false;

        foreach ($files as $file) {
            // @todo check if all images are uploaded or send error
            $image = new Image();
            $image->setImageFile($file)->setTrick($trick);
            if($trick->getMainImage() === null) {
                $changed = $image->getImageName();
            }
            $entityManager->persist($image);
            $images->add($image);
        }

        $entityManager->flush();

        $view = $this->renderView('tricks/list-images.html.twig', ['images' => $images]);
        return new JsonResponse(['view' => $view, 'changed' => $changed]);
    }
}
