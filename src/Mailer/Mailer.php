<?php

declare(strict_types=1);

namespace App\Mailer;

use App\Entity\User;

class Mailer
{
    /** @var \Swift_Mailer */
    private $mailer;

    /** @var \Twig_Environment */
    private $twig;

    /** @var string  */
    private $mailFrom;

    /**
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $twig
     * @param string $mailFrom
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, string $mailFrom)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->mailFrom = $mailFrom;
    }

    /**
     * @param User $user
     *
     * @throws \Exception
     *
     * @return void
     */
    public function sendConfirmationEmail(User $user): void
    {
        $body = $this->twig->render('email/registration.html.twig', [
            'user' => $user,
        ]);

        $message = (new \Swift_Message())
            ->setFrom($this->mailFrom)
            ->setTo($user->getEmail())
            ->setBody($body, 'text/html')
            ->setSubject('Welcome');

        $this->mailer->send($message);
    }
}