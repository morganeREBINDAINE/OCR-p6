<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthenticationController extends AbstractController
{
    /**
     * @Route("/connexion", name="app_login")
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
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
    public function register(Request $request, UserPasswordEncoderInterface $encoder, UserRepository $userRepository)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $userRepository->save($user);
            $this->addFlash('success', 'Vous avez bien été inscrit. Veuillez cliquez sur le lien de validation de compte envoyé sur le mail indiqué.');
            return $this->redirectToRoute('success');
        }

        return $this->render('authentication/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
