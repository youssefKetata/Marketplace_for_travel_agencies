<?php
namespace App\Service;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Response;

class Mailer
{

    public function __construct(Private MailerInterface $mailer)
    {
    }

    public function sendEmail(
        $to = 'wassimzouch34@gmail.com',
        $content= 'See Twig integration for better HTML integration!',
        $subject= 'Time for Symfony Mailer!'
    ): void
    {
        $email = (new Email())
            ->from('hello@example.com')
            ->to($to)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->text('Sending emails is fun again!')
            ->html($content);

        $this->mailer->send($email);

        // ...
    }
}