<?php

namespace App\Services;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Mailer
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(UrlGeneratorInterface $router, MailerInterface $mailer)
    {
        $this->router = $router;
        $this->mailer = $mailer;
    }

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
        }
    }
}
