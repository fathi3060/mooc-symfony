<?php
//comme on ne peut plus creer de bundle avec cette version de symfony , on creer juste le controller

// src/Controller/CoreController.php

namespace App\Controller\OC;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;


class CoreController extends Controller 
{
    //la route est indiqué dans le fichier toutes.yaml
    // correspond à : path: /
    public function IndexAction()
    {
        //on reroute vers la page qui affiche les 3 dernières annonces
        return $this->redirectToRoute('cb_index_action'); 
    }

    //la route est indiqué dans le fichier toutes.yaml
    // correspond à : path: /contact
    public function ContactAction(Request $request)
    {
        
        // On récupère la session depuis la requête, en argument du contrôleur
        $session = $request->getSession();
        // Et on définit notre message
        $session->getFlashBag()->add('warning', 'La page de contact n’est pas encore disponible, merci de revenir plus tard.');

        //on reroute vers la page d'accueil
        return $this->redirectToRoute('oc_core_home');

    }
}