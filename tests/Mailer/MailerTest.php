<?php

declare(strict_types=1);

namespace App\Tests\Mailer;

use App\Entity\User;
use App\Mailer\Mailer;
use PHPUnit\Framework\TestCase;

class MailerTest extends TestCase
{
    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testConfirmationEmail(): void
    {
        [$swiftMailerMock, $twigMock, $user] = $this->prepareDataTestConfirmationEmail();

        $mailer = new Mailer($swiftMailerMock, $twigMock, 'lol@enauk.com');
        $mailer->sendConfirmationEmail($user);
    }

    /**
     * @return array
     */
    private function prepareDataTestConfirmationEmail(): array
    {
        $user = new User();
        $user->setEmail('some@email.org');

        $swiftMailerMock = $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $swiftMailerMock->expects($this->once())->method('send')
            ->with($this->callback(function ($subject) use($user) {
                $messageStr = (string)$subject;
dump($messageStr);
                return strpos($messageStr, 'From: lol@enauk.com') !== false
                    && strpos($messageStr, 'Content-Type: text/html; charset=utf-8') !== false
                    && strpos($messageStr, 'Subject: Welcome') !== false
                    && strpos($messageStr, 'To: ' . $user->getEmail()) !== false;
            }));

        $twigMock = $this->getMockBuilder(\Twig_Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $twigMock->expects($this->once())->method('render')
            ->with(
                'email/registration.html.twig',
                [
                    'user' => $user,
                ]
            );

        return [
            $swiftMailerMock,
            $twigMock,
            $user,
        ];
    }
}