<?php
// src/Service/Antispam/OCAntispam.php

namespace App\Service\Antispam;

class OCAntispam
{
    private $mailer;
    private $locale;
    private $minLength;

    public function __construct(\Swift_Mailer $mailer)
//    public function __construct($minLength)
    {
        $this->mailer    = $mailer;
     #   $this->locale    = $locale;
     #   $this->minLength = (int) $minLength;
    }
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
    public function setminLength($minLength)
    {
        $this->minLength = $minLength;
    }

    /**
     * Vérifie si le texte est un spam ou non
     *
     * @param string $text
     * @return bool
     */
    public function isSpam($text)
    {
        return strlen($text) < $this->minLength;
    }
}