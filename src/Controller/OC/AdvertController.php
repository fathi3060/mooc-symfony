<?php
// src/Controller/AdvertController.php

namespace App\Controller\OC;

use App\Entity\Advert;
use App\Entity\Image;
use App\Entity\Application;
use App\Entity\Category;
use App\Entity\AdvertSkill;
use App\Entity\Skill;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
//use App\Service\Antispam\OCAntispam;
use Symfony\Component\HttpFoundation\Response;
use App\Form\AdvertType;
use App\Form\AdvertEditType;
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use App\PlatformBundle\Event\PlatformEvents;
use App\PlatformBundle\Event\MessagePostEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


/**
 * @Route("/advert")
 */
class AdvertController extends AbstractController
{
    private $params;
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    /**
     * @Route("/{page}", name="oc_advert_index", requirements={"page" = "\d+"}, defaults={"page" = 1})
     */
    public function indexAction($page)
    {
        if ($page < 1) {
            throw new NotFoundHttpException('Page "' . $page . '" inexistante.');
        }

        $nbPerPage = $this->params->get('nb_per_page');

        $em = $this->getDoctrine()->getManager();
        //ancienne méthode
        //$listAdverts = $em->getRepository(Advert::class)->findAll();
        //nouvelle méthode
        $listAdverts = $em->getRepository(Advert::class)->getAdverts($page, $nbPerPage);

        // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
        $nbPages = ceil(count($listAdverts) / $nbPerPage);

        // Si la page n'existe pas, on retourne une 404
        if ($page > $nbPages) {
            throw $this->createNotFoundException("La page " . $page . " n'existe pas.");
        }

        // Et modifiez le 2nd argument pour injecter notre liste
        return $this->render('Advert/index.html.twig', array(
            'listAdverts' => $listAdverts,
            'nbPages'     => $nbPages,
            'page'        => $page,
        ));
    }

    //mis de coté car maintenant on utilise ParamConverter
    //*@Route("/view/{id}", name="oc_advert_view", requirements={"id" = "\d+"})
    //* @ParamConverter("advert", options={"mapping": {"advert_id": "id"}})
    // public function viewAction(Advert $advert)
    //public function viewAction($id)

    /**
     * @Route("/view/{id}", name="oc_advert_view", requirements={"advert_id" = "\d+"})
     * @ParamConverter("advert", options={"mapping": {"advert_id": "id"}})
     */
    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        //on utilise ParamConverter donc plus besoin de rechercher par l'ID nooooon
        // On récupère l'annonce $id
        $advert = $em->getRepository(Advert::class)->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }

        // On récupère la liste des candidatures de cette annonce
        $listApplications = $em
            ->getRepository(Application::class)
            ->findBy(array('advert' => $advert));

        // On récupère maintenant la liste des AdvertSkill
        $listAdvertSkills = $em
            ->getRepository(AdvertSkill::class)
            ->findBy(array('advert' => $advert));

        return $this->render('Advert/view.html.twig', array(
            'advert'           => $advert,
            'listApplications' => $listApplications,
            'listAdvertSkills' => $listAdvertSkills
        ));
    }

//@Security("has_role('ROLE_AUTEUR') or has_role('ROLE_ADMIN')")
    /**
     * @Route("/add", name="oc_advert_add")
     */
    public function addAction(Request $request)
    {
        //on va plutot utilisé les annotations en utilisant le bundle SensioFrameworkExtraBundle
        // On vérifie que l'utilisateur dispose bien du rôle ROLE_AUTEUR
        // if (!$this->get('security.authorization_checker')->isGranted('ROLE_AUTEUR')) {
        //     // Sinon on déclenche une exception « Accès interdit »
        //     throw new AccessDeniedException('Accès limité aux auteurs.');
        // }

        $advert = new Advert();
        $form = $this->createForm(AdvertType::class, $advert);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            

            // On crée l'évènement avec ses 2 arguments
            $event = new MessagePostEvent($advert->getContent(), $this->getUser());
            $dispatcher = new EventDispatcher();

            // On déclenche l'évènement
            //$this->get('event_dispatcher')->dispatch(PlatformEvents::POST_MESSAGE, $event);
            $dispatcher->dispatch(PlatformEvents::POST_MESSAGE, $event);

            // On récupère ce qui a été modifié par le ou les listeners, ici le message
            $advert->setContent($event->getMessage());

            $em = $this->getDoctrine()->getManager();
            $em->persist($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            return $this->redirectToRoute('oc_advert_view', array('id' => $advert->getId()));
            //return $this->redirectToRoute('oc_advert_view', array('advert_id' => $advert->getId()));
        }
    
        return $this->render('Advert/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/edit/{id}", name="oc_advert_edit", requirements={"id" = "\d+"})
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $advert = $em->getRepository(Advert::class)->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }

        /*
        // La méthode findAll retourne toutes les catégories de la base de données
        $listCategories = $em->getRepository(Category::class)->findAll();

        // On boucle sur les catégories pour les lier à l'annonce
        foreach ($listCategories as $category) {
            $advert->addCategory($category);
        }
        */

        // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
        // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

        // Étape 2 : On déclenche l'enregistrement
        //$em->flush();

        $form = $this->createForm(AdvertEditType::class, $advert);
        
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            //echo ("qsdfdsqfsdqfdsqfqsdfsdfsdfdsqfqsd");
            // Inutile de persister ici, Doctrine connait déjà notre annonce
            $em->flush();
            // $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');
            $this->addFlash('notice', 'Annonce bien modifiée.');

            // Puis on redirige vers la page de visualisation de cette annonce
            return $this->redirectToRoute('oc_advert_view', ['id' => $advert->getId()]);
        }

        return $this->render('Advert/edit.html.twig', array(
            'advert' => $advert,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/delete/{id}", name="oc_advert_delete", requirements={"id" = "\d+"})
     */
    public function deleteAction(Request $request, $id)
    {  
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $advert = $em->getRepository(Advert::class)->find($id);
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }

        /*
        // On boucle sur les catégories de l'annonce pour les supprimer
        foreach ($advert->getCategories() as $category) {
            $advert->removeCategory($category);
        }
        */

        // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
        // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

        // On déclenche la modification
        //$em->flush();
        // On crée un formulaire vide, qui ne contiendra que le champ CSRF
        // Cela permet de protéger la suppression d'annonce contre cette faille
        $form = $this->get('form.factory')->create();

        //if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        if ($request->isMethod('POST')) {

            //suppression de ses catégories attachées
            $em->getRepository(Advert::class)->removeCategoriesbyAdvert($em, $advert);
            $em->getRepository(Advert::class)->removeSkillsbyAdvert($em, $advert);

            $em->remove($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");

            return $this->redirectToRoute('oc_advert_index');

        }

        return $this->render('Advert/delete.html.twig', array(
            'advert' => $advert,
            'form'   => $form->createView(),
        ));

    }

    public function menuAction($limit)
    {
        $em = $this->getDoctrine()->getManager();

        $listAdverts = $em->getRepository(Advert::class)->findBy(
            array(),                 // Pas de critère
            array('date' => 'desc'), // On trie par date décroissante
            $limit,                  // On sélectionne $limit annonces
            0                        // À partir du premier
        );

        return $this->render('Advert/menu.html.twig', array(
            // Tout l'intérêt est ici : le contrôleur passe
            // les variables nécessaires au template !
            'listAdverts' => $listAdverts,
        ));
    }

    // Dans un contrôleur, celui que vous voulez
    public function editImageAction($advertId)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce
        $advert = $em->getRepository(Advert::class)->find($advertId);

        // On modifie l'URL de l'image par exemple
        $advert->getImage()->setUrl('test.png');

        // On n'a pas besoin de persister l'annonce ni l'image.
        // Rappelez-vous, ces entités sont automatiquement persistées car
        // on les a récupérées depuis Doctrine lui-même
        
        // On déclenche la modification
        $em->flush();

        return new Response('OK');
    }

    public function listAction()
    {
        $listAdverts = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Advert::class)
            ->getAdvertWithApplications();

        foreach ($listAdverts as $advert) {
            // Ne déclenche pas de requête : les candidatures sont déjà chargées !
            // Vous pourriez faire une boucle dessus pour les afficher toutes
            $advert->getApplications();
        }

        // …
    }
    
    // Fonction appeler grace à notre route : /config/routes.yaml
    // qui ensuite appel notre service que l'on a configuré dans le fichier : src/OC/PlatformBundle/Resources/config/services.yml
    // et notre service va faire appel à la classe : OC\PlatformBundle\Purge\Purge
    public function purgeAction($days, Request $request, ContainerInterface $container)
    {
        $purge = $container->get('oc_platform.advert_purger');
        $purge->purge($days);
        //return new Response('Purge effectuee');
        // On ajoute un message flash arbitraire
        $request->getSession()->getFlashBag()->add('warning', 'Les annonces plus vieilles que ' . $days . ' jours ont été purgées.');

        // On redirige vers la page d'accueil
        return $this->redirectToRoute('oc_core_home');

    }

    /**
     * @Route("/test", name="oc_advert_test")
     */
    public function testAction(ValidatorInterface $validator)
    {
        $advert = new Advert;
        
        $advert->setDate(new \Datetime());  // Champ « date » OK
        $advert->setTitle('abc');           // Champ « title » incorrect : moins de 10 caractères
        //$advert->setContent('blabla');    // Champ « content » incorrect : on ne le définit pas
        $advert->setAuthor('A');            // Champ « author » incorrect : moins de 2 caractères

        // On récupère le service validator
        //$validator = $this->get('validator');

        // On déclenche la validation sur notre object
        $listErrors = $validator->validate($advert);

        // Si $listErrors n'est pas vide, on affiche les erreurs
        if(count($listErrors) > 0) {
        // $listErrors est un objet, sa méthode __toString permet de lister joliement les erreurs
            return new Response((string) $listErrors);
        } else {
            return new Response("L'annonce est valide !");
        }
    }

    public function translationAction($name) : response
    {
        return $this->render('Advert/translation.html.twig', array(
            'name' => $name
        ));
    }

    /**
     * @ParamConverter("json")
     */
    public function ParamConverterAction($json)
    {
        return new Response(print_r($json, true));
    }
}
