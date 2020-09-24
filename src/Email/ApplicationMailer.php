<?php
// src/Email/ApplicationMailer.php

namespace App\Email;

use App\Entity\Application;

class ApplicationMailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendNewNotification(Application $application)
    {
        $message = new \Swift_Message(
            'Nouvelle candidature',
            'Vous avez reçu une nouvelle candidature.'
        );

        $message
            ->setTo($application->getAdvert()->getEmail()) // attribut "email"
            ->setFrom('admin@votresite.com')
            ->setBody("Message ,,,,,");

        $this->mailer->send($message);

    }
}
