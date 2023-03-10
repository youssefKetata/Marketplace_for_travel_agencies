<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Twig\Environment;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class Mail
{
    public function __construct(private MailerInterface $mailer, private Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendEmail(string $templateName, array $templateData, string $subject, string $to): void
    {
        //$template = $this->twig->load($templateName);
        //$body = $template->render($templateData);
        $dsn = 'smtp://user:pass@smtp.example.com:25';
        $transport = Transport::fromDsn($dsn);

        $email = (new TemplatedEmail())
            ->from('your_email@example.com')
            ->to($to)
            ->subject($subject)
            // path of the Twig template to render
            ->htmlTemplate($templateName)
            //->text();
            //->html($body);
                //all variables of seller passed in context
            ->context([
                'expiration_date' => new \DateTime('+7 days'),
                'username' => 'foo',
            ]);
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            // some error prevented the email sending; display an
            // error message or try to resend the message
        }


    }

}