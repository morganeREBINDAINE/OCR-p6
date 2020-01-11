<?php

namespace App\Controller;

use App\Entity\Token;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\ResetPasswordType;
use App\Repository\TokenRepository;
use App\Repository\UserRepository;
use App\Services\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthenticationController extends AbstractController
{
    /**
     * @Route("/connexion", name="app_login")
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function login(AuthenticationUtils $authenticationUtils, UserRepository $repository): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('home');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('authentication/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/inscription", name="registration")
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
     */
    public function validate(Token $validToken, EntityManagerInterface $entityManager)
    {
        if ($validToken && $validToken->getType() === 'subscription' && $validToken->getAccessed() === null) {
            $validToken->setAccessed(new \DateTimeImmutable());
            $entityManager->flush();
            $this->addFlash('success', 'Félicitations ! Votre compte est actif, vous pouvez désormais vous connecter et gérer les figures.');
            return $this->render('home/message.html.twig');
        }

        // @todo invalid token message
        throw new NotFoundHttpException();
    }

    /**
     * @Route("/mot-de-passe-oublie", name="forgotten_password")
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

        return $this->render('authentication/forgotten_password.html.twig', [

        ]);
    }

    /**
     * @Route("/reset/{token}", name="reset_password")
     */
    public function resetPassword(Token $validToken, EntityManagerInterface $entityManager, Request $request, UserPasswordEncoderInterface $encoder, TokenRepository $tokenRepository)
    {
        if ($validToken && $validToken->getType() === 'forgotten_password' && $validToken->getAccessed() === null) {
            $user = $validToken->getUser();
            $form = $this->createForm(ResetPasswordType::class, $user);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
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
        // @todo invalid token message
        throw new NotFoundHttpException();
    }
}
