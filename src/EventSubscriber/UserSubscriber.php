<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\UserPreferences;
use App\Event\UserRegisterEvent;
use App\Mailer\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    /** @var \Swift_Mailer */
    private $mailer;

    /** @var EntityManagerInterface */
    private $em;

    /** @var string */
    private $defaultLocale;

    /**
     * @param Mailer $mailer
     * @param EntityManagerInterface $em
     * @param string $defaultLocale
     */
    public function __construct(Mailer $mailer, EntityManagerInterface $em, string $defaultLocale)
    {
        $this->mailer = $mailer;
        $this->em = $em;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @return array|void
     */
    public static function getSubscribedEvents()
    {
        return [
            UserRegisterEvent::NAME => 'onUserRegistered',
        ];
    }

    /**
     * @param UserRegisterEvent $event
     *
     * @throws \Exception
     *
     * @return void
     */
    public function onUserRegistered(UserRegisterEvent $event): void
    {
        $preferences = new UserPreferences();
        $preferences->setLocale($this->defaultLocale);
        $this->em->persist($preferences);

        $user = $event->getRegisteredUser();
        $user->setPreferences($preferences);
        $this->em->flush();

        $this->mailer->sendConfirmationEmail($event->getRegisteredUser());
    }
}