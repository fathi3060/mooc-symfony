<?php
// src//DoctrineListener/ApplicationCreationListener.php

namespace App\DoctrineListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use App\Email\ApplicationMailer;
use App\Entity\Application;


class ApplicationCreationListener
{
    /**
     * @var ApplicationMailer
     */
    private $applicationMailer;

    public function __construct(ApplicationMailer $applicationMailer)
    {
        $this->applicationMailer = $applicationMailer;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        // On ne veut envoyer un email que pour les entités Application
        if (!$entity instanceof Application) {
            return;
        }

        try{
            $this->applicationMailer->sendNewNotification($entity);
        } catch (\Error $e){
            var_dump('Erreur fonction : ',  $e->getMessage(), "\n");
        };
    }
}
