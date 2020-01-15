<?php

namespace App\Services;

use App\Entity\{Token, User};
use App\Repository\TokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\{Exception\TransportExceptionInterface, MailerInterface};
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Mailer
{
    /** @var UrlGeneratorInterface */
    private $router;
    /** @var MailerInterface */
    private $mailer;
    /** @var TokenRepository */
    private $tokenRepository;
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * @param UrlGeneratorInterface  $router
     * @param MailerInterface        $mailer
     * @param TokenRepository        $tokenRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UrlGeneratorInterface $router, MailerInterface $mailer, TokenRepository $tokenRepository, EntityManagerInterface $entityManager)
    {
        $this->router = $router;
        $this->mailer = $mailer;
        $this->tokenRepository = $tokenRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user
     * @throws TransportExceptionInterface
     */
    public function sendForgottenPasswordMail(User $user) {
        $token = $this->tokenRepository->findOneBy(['user' => $user, 'type' => 'forgotten_password', 'accessed' => null]);

        if ($token === null) {
            $token = new Token(TOKEN::TYPE_FORGOTTEN_PASSWORD, $user);
            $this->entityManager->persist($token);
            $this->entityManager->flush();
            $user->addToken($token);
        }

        $url = $this->router->generate('reset_password', [
            'token' => $token->getToken(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);


        $email = (new Email())
            ->from('contact@snow-tricks.com')
            ->to($user->getEmail())
            ->subject('Lien de réinitialisation de votre mot de passe')
            ->text('Réinitialiser le mot de passe')
            ->html('<p>Cliquez ici pour réinitialiser votre mot de passe: <a href="'.$url.'">'.$url.'</a> </p>');

        $this->mailer->send($email);
    }

    /**
     * @param User $user
     * @throws TransportExceptionInterface
     */
    public function sendSubscriptionMail(User $user)
    {
        if ($user->isValid() === false) {
            $url = $this->router->generate('validation', [
                'token' => $user->getSubscriptionToken()->getToken(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            $email = (new Email())
                ->from('contact@snow-tricks.com')
                ->to($user->getEmail())
                ->subject('Merci de valider votre compte')
                ->text('Validation du compte')
                ->html('<p>Cliquez ici pour valider le compte: <a href="'.$url.'">'.$url.'</a> </p>');

            $this->mailer->send($email);
        }
    }
}
