<?php

namespace App\Controller;

use App\Entity\{Token, User};
use App\Form\{RegistrationType, ResetPasswordType};
use App\Repository\UserRepository;
use App\Services\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\{RedirectResponse, Request, Response};
use Symfony\Component\Security\{Core\Encoder\UserPasswordEncoderInterface, Http\Authentication\AuthenticationUtils};

class AuthenticationController extends AbstractController
{
    /**
     * @Route("/connexion", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('authentication/login.html.twig', ['error' => $authenticationUtils->getLastAuthenticationError()]);
    }

    /** @Route("/logout", name="app_logout") */
    public function logout(){}

    /**
     * @Route("/inscription", name="registration")
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $encoder
     * @param UserRepository               $userRepository
     * @param Mailer                       $mailer
     *
     * @return RedirectResponse|Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder, UserRepository $userRepository, Mailer $mailer)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $userRepository->save($user);
            $mailer->sendSubscriptionMail($user);
            $this->addFlash('success', 'Vous avez bien été inscrit. Veuillez cliquez sur le lien de validation de compte envoyé sur le mail que vous avez indiqué.');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('authentication/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/validation/{token}", name="validation")
     * @param Token                  $validToken
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     * @throws \Exception
     */
    public function validate(Token $validToken, EntityManagerInterface $entityManager)
    {
        if ($validToken && $validToken->getType() === 'subscription' && $validToken->getAccessed() === null) {
            $validToken->setAccessed(new \DateTimeImmutable());
            $entityManager->flush();
            $this->addFlash('success', 'Félicitations ! Votre compte est actif, vous pouvez désormais vous connecter et gérer les figures.');
            return $this->render('home/message.html.twig');
        }
        throw new NotFoundHttpException();
    }

    /**
     * @Route("/mot-de-passe-oublie", name="forgotten_password")
     * @param Request        $request
     * @param UserRepository $userRepository
     * @param Mailer         $mailer
     *
     * @return RedirectResponse|Response
     */
    public function forgottenPassword(Request $request, UserRepository $userRepository, Mailer $mailer)
    {
        if ($request->isMethod('POST') && $request->request->get('email')) {
            if ($user = $userRepository->findOneBy(['email' => $request->request->get('email')])) {
                $mailer->sendForgottenPasswordMail($user);
            }
            $this->addFlash('success-forgotten-password', 'Un email de réinitialisation de mot de passe a bien été envoyé à l\'adresse email indiquée.');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('authentication/forgotten_password.html.twig');
    }

    /**
     * @Route("/reset/{token}", name="reset_password")
     * @param Token                        $validToken
     * @param EntityManagerInterface       $entityManager
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function resetPassword(Token $validToken, EntityManagerInterface $entityManager, Request $request, UserPasswordEncoderInterface $encoder)
    {
        if ($validToken && $validToken->getType() === 'forgotten_password' && $validToken->getAccessed() === null) {
            $user = $validToken->getUser();
            $form = $this->createForm(ResetPasswordType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $password = $encoder->encodePassword($user, $user->getPassword());
                $validToken->setAccessed(new \DateTimeImmutable());
                $user->setPassword($password);
                $entityManager->flush();
                $this->addFlash('success', 'Félicitations, votre mot de passe a bien été réinitialisé.');
                return $this->redirectToRoute('app_login');
            }
            return $this->render('authentication/reset_password.html.twig', [
                'form' => $form->createView()
            ]);
        }
        throw new NotFoundHttpException();
    }
}
