<?php
// src/Controller/EmailController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailController extends AbstractController
{
    #[Route('/send-email', name: 'send_email')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('you@example.com')
            ->to('recipient@example.com')
            ->subject('Hello Email')
            ->text('Sending emails is fun!')
            ->html('<p>Sending emails is <b>fun</b>!</p>');

        $mailer->send($email);

        return new Response('Email sent!');
    }
}
