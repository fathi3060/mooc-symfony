<?php
// src/Controller/CoreBundle/DefaultController.php

namespace App\Controller\CoreBundle;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/corebundle")
 */
class DefaultController extends AbstractController
{

    /**
     * @Route("/", name="cb_index_action")
     */
    public function indexAction()
    {
        // $listAdverts = array(
        //     array('id' => 2, 'title' => 'Recherche développeur Symfony'),
        //     array('id' => 5, 'title' => 'Mission de webmaster'),
        //     array('id' => 9, 'title' => 'Offre de stage webdesigner'),
        // );

        // return $this->render('CoreBundle/index.html.twig', array(
        //     // Tout l'intérêt est ici : le contrôleur passe
        //     // les variables nécessaires au template !
        //     'listAdverts' => $listAdverts,
        // ));
        
        //modification : la page par défaut devient le lien accueil 
        return $this->redirectToRoute('oc_advert_index');


    }


    /**
     * @Route("/contact", name="cb_contact_action")
     */
    public function contactAction()
    {
        //AFFICHAGE DU MESSAGE FLASH
        $this->addFlash(
            'warning',
            "Message Flash : La page de contact n'est pas encore disponible. Merci de revenir plus tard."
        );
        //REDIRECTION VERS LA PAGE D'ACCUEIL
        return $this->redirectToRoute('cb_index_action');
    }

    public function menuAction($limit)
    {
        // On fixe en dur une liste ici, bien entendu par la suite
        // on la récupérera depuis la BDD !
        $listAdverts = array(
            array('id' => 2, 'title' => 'Recherche développeur Symfony'),
            array('id' => 5, 'title' => 'Mission de webmaster'),
            array('id' => 9, 'title' => 'Offre de stage webdesigner'),
        );

        return $this->render('Advert/menu.html.twig', array(
            // Tout l'intérêt est ici : le contrôleur passe
            // les variables nécessaires au template !
            'listAdverts' => $listAdverts,
        ));
    }

}
