<?php
// src/OC/PlatformBundle/Purge/Purge.php

namespace App\Purge;

//use Doctrine\ORM\Entity;
use Doctrine\ORM\EntityManager;
use App\Entity\Advert;
class Purge
{
    private $em;
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    public function purge($days)
    {

        //partie suppression
        /******************************* */
        // on récupère les annonces n'ayant pas de candidatures dont la date de mofications est infèrieurs à x jours
        //on supprime d'abord les catégories associées à cette annonce, puis les compétences et enfin l'annonce en elle meme
        $adverts = $this->em->getRepository(Advert::class)->getAdvertNoApplication($days);
        //echo($advert->count());
        foreach ($adverts as $advert) {
            $this->em->getRepository(Advert::class)->removeCategoriesbyAdvert($this->em, $advert);
            $this->em->getRepository(Advert::class)->removeSkillsbyAdvert($this->em, $advert);
            echo ("suppression de l'annonce ayant pour id ". $advert->getId() . " </br>");
            $this->em->remove($advert);
        }
        
        $this->em->flush();
        /******************************* */

    }
}
