<?php
// src/PlatformBundle/Bigbrother/MessageListener.php

namespace App\PlatformBundle\Bigbrother;
use App\PlatformBundle\Event\MessagePostEvent;
use App\PlatformBundle\Event\PlatformEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MessageListener  implements EventSubscriberInterface
{
    protected $notificator;
    protected $listUsers = array();

    // public function __construct(MessageNotificator $notificator, $listUsers)
    // {
    //     $this->notificator = $notificator;
    //     $this->listUsers = $listUsers;
    // }

// La méthode de l'interface que l'on doit implémenter, à définir en static
    static public function getSubscribedEvents()
    {
        // On retourne un tableau « nom de l'évènement » => « méthode à exécuter »
        return array(
            PlatformEvents::POST_MESSAGE    => 'processMessage',
            //PlatformEvents::AUTRE_EVENEMENT => 'autreMethode',
            // ...
        );
    }

    public function processMessage(MessagePostEvent $event)
    {
        // On active la surveillance si l'auteur du message est dans la liste
        if (in_array($event->getUser()->getUsername(), $this->listUsers)) {
            // On envoie un e-mail à l'administrateur
            $this->notificator->notifyByEmail($event->getMessage(), $event->getUser());
        }
    }
    // public function autreMethode()
    // {
    // // ...
    // }
}
