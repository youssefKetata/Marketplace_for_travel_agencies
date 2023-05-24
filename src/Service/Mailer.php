<?php

namespace App\Service;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;


class Mailer
{

    public function __construct(private readonly MailerInterface $mailer)
    {
    }
//key name : marketplcae
//apikey: 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
//server: smtp.sendgrid.net
//username: apikey
//port: 25,587 	(for unencrypted/TLS connections)
//port: 465 	(for SSL connections)

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmail(string $toEmail, string $subject, string $text, string $html): void
    {
        $api_key = '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';
        $server = 'smtp.sendgrid.net';
        $port = '25, 587'; //for unencrypted/TLS connections
        $ports = '465'; //(for SSL connections
        $Username = 'api_key';
        $Password = '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';
        $sender = 'jusuf.ktata@enetcom.u-sfax.tn';
        $email = (new Email())
            ->from($sender)
            ->to($toEmail)
            ->subject($subject)
            ->text($text)
            ->html($html);
        $this->mailer->send($email);
    }
}