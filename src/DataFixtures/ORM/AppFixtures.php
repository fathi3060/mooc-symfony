<?php
namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Advert;
use App\Entity\Category;
use App\Entity\Skill;
use App\Entity\AdvertSkill;
use App\Entity\Application;
use App\Entity\User;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //enregistrement des catégories
        $this->loadCategories($manager);

        // //enregistrement des compétences : skills
        $this->loadSkill($manager);

        // //enregistrement de plusieurs advert : annonce
        for($i=1; $i<=10; $i++)
        {
            $this->loadAdvert($manager);
        }

        //création  d'utilisateurs
        $this->loadUser($manager);

    }

    private function loadAdvert(ObjectManager $manager)
    {
        // Création de l'entité Advert
        $advert = new Advert();
        $arrX = array('PHP', 'Symfony', 'C++', 'Java', 'Photoshop', 'Blender', 'Bloc-note');
        $randIndex = array_rand($arrX);
        $advert->setTitle('Recherche développeur : ' . $arrX[$randIndex] . ' ' . array_rand(range(1,100,1)) . '.' );
        //$advert->setAuthor('Alexandre');
        $arrAuthor = array("Alexandre", "Paul", "Susan", "Frank");
        $randIdxAuthor = array_rand($arrAuthor);
        $advert->setAuthor($arrAuthor[$randIdxAuthor]);

        $advert->setEmail("recipient@example.com");
        $advert->setContent("Nous recherchons un développeur " . $arrX[$randIndex] . " débutant sur Lyon. Blabla…");


        $date = new \DateTime();
        $date->modify('-'. random_int(0, 15) .' day');
        //echo $date->format('Y-m-d');
        
        $advert->setDate($date);
        $advert->setUpdatedAt($date);

        // On récupère toutes les compétences possibles
        $listSkills = $manager->getRepository(Skill::class)->findAll();

        // Pour chaque compétence
        foreach ($listSkills as $skill) {
            // On crée une nouvelle « relation entre 1 annonce et 1 compétence »
            $advertSkill = new AdvertSkill();

            // On la lie à l'annonce, qui est ici toujours la même
            $advertSkill->setAdvert($advert);
            // On la lie à la compétence, qui change ici dans la boucle foreach
            $advertSkill->setSkill($skill);

            // Arbitrairement, on dit que chaque compétence est requise au niveau 'Expert'
            $advertSkill->setLevel('Expert');

            // Et bien sûr, on persiste cette entité de relation, propriétaire des deux autres relations
            $manager->persist($advertSkill);
        }



        if (rand(0, 1)) 
        {
            //enregistrement des candidatures
            // Création d'une première candidature
            $application1 = new Application();
            $application1->setAuthor('Marine');
            $application1->setContent("J'ai toutes les qualités requises.");

            // Création d'une deuxième candidature par exemple
            $application2 = new Application();
            $application2->setAuthor('Pierre');
            $application2->setContent("Je suis très motivé.");

            // On lie les candidatures à l'annonce
            $application1->setAdvert($advert);
            $application2->setAdvert($advert);

            //Etape 1 : On « persiste » l'entité
            $manager->persist($advert);
            // Étape 1 bis : si on n'avait pas défini le cascade={"persist"},
            // on devrait persister à la main l'entité $image
            // $em->persist($image);

            // Étape 1 ter : pour cette relation pas de cascade lorsqu'on persiste Advert, car la relation est
            // définie dans l'entité Application et non Advert. On doit donc tout persister à la main ici.
            $manager->persist($application1);
            $manager->persist($application2);

            //Etape 2 : On « flush » tout ce qui a été persisté avant
            $manager->flush();
        }

        // Doctrine ne connait pas encore l'entité $advert. Si vous n'avez pas défini la relation AdvertSkill
        // avec un cascade persist (ce qui est le cas si vous avez utilisé mon code), alors on doit persister $advert
        $manager->persist($advert);

        // On déclenche l'enregistrement
        $manager->flush();
    }

    private function loadCategories(ObjectManager $manager)
    {
                // Liste des noms de catégorie à ajouter
        $names = array(
            'Développement web',
            'Développement mobile',
            'Graphisme',
            'Intégration',
            'Réseau'
        );

        foreach ($names as $name) {
            // On crée la catégorie
            $category = new Category();
            $category->setName($name);

            // On la persiste
            $manager->persist($category);
        }

        // On déclenche l'enregistrement de toutes les catégories
        $manager->flush();

    }
    
    private function loadSkill($manager)
    {
        // Liste des noms de compétences à ajouter
        $names = array('PHP', 'Symfony', 'C++', 'Java', 'Photoshop', 'Blender', 'Bloc-note');

        foreach ($names as $name) {
            // On crée la compétence
            $skill = new Skill();
            $skill->setName($name);

            // On la persiste
            $manager->persist($skill);
        }
        // On déclenche l'enregistrement de toutes les compétences
        $manager->flush();
    }

    public function loadUser(ObjectManager $manager)
    {
        // Les noms d'utilisateurs à créer
        $listNames = array('alexandre', 'marine', 'anna');

        foreach ($listNames as $name) {
            // On crée l'utilisateur
            $user = new User;

            // Le nom d'utilisateur et le mot de passe sont identiques pour l'instant
            $user->setUsername($name);
            $user->setPassword($name);
            

            // On ne se sert pas du sel pour l'instant
            $user->setSalt('');
            // On définit uniquement le role ROLE_USER qui est le role de base
            $user->setRoles(array('ROLE_AUTEUR'));
            //$user->setEnabled = true;

            $user->setEmail('monemail'. array_rand(range(1,100,1)) . '@mail.com');
            // On le persiste
            $manager->persist($user);
        }
        // On déclenche l'enregistrement
        $manager->flush();
    }
}
