<?php
// src/PlatformBundle/Twig/AntispamExtension.php

namespace App\PlatformBundle\Twig;

use App\Service\Antispam\OCAntispam;
#use Symfony\Bundle\TwigBundle\TwigBundle;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

#class AntispamExtension extends \Twig_Extension

class AntispamExtension extends AbstractExtension
{
    /**
     * @var OCAntispam
     */
    private $ocAntispam;

    public function __construct(OCAntispam $ocAntispam)
    {
        $this->ocAntispam = $ocAntispam;
    }

    public function checkIfArgumentIsSpam($text)
    {
        return $this->ocAntispam->isSpam($text);
    }

    // Twig va exécuter cette méthode pour savoir quelle(s) fonction(s) ajoute notre service
    public function getFunctions()
    {
        return array(
        #new \Twig_SimpleFunction('checkIfSpam', array($this, 'checkIfArgumentIsSpam')),
        new \Twig_SimpleFunction('checkIfSpam', array($this->ocAntispam, 'isSpam')),
        #new TwigFunction('checkIfSpam', array($this->ocAntispam, 'isSpam')),
        );
    }

    // La méthode getName() identifie votre extension Twig, elle est obligatoire
    public function getName()
    {
        return 'OCAntispam';
    }
}
