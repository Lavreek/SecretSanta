<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerHandler
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendInviteToGame(string $email, $identifier) : void
    {
        $mail = (new TemplatedEmail())
            ->from('noreply@yourdwell.ru')
            ->to($email)
            ->subject('Тайный санта, найдись!')
            ->htmlTemplate('mailing/invite_email.html.twig')
            ->context([
                'identifier' => $identifier
            ]);

        $this->mailer->send($mail);
    }

    public function sendStartGame(string $email, $identifier) : void
    {
        $mail = (new TemplatedEmail())
            ->from('noreply@yourdwell.ru')
            ->to($email)
            ->subject('Тайный санта, игра началась!')
            ->htmlTemplate('mailing/start_email.html.twig')
            ->context([
                'identifier' => $identifier
            ]);

        $this->mailer->send($mail);
    }
}