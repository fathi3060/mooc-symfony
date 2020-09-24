<?php

namespace App\Repository;

use App\Entity\Advert;
use App\Entity\AdvertSkill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use DateTime;

/**
 * @method Advert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advert[]    findAll()
 * @method Advert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advert::class);
    }

    //********************************* */
    //concerne la purge

    //selection des annonces sans candidatures
    public function getAdvertNoApplication($days)
    {
        $dateNow = new DateTime("now");
        $dateNow = $dateNow->sub(new \DateInterval('P' . $days . 'D'));

        //echo ($dateNow->format('Y-m-d H:i:s'));

        $queryBuilder = $this->createQueryBuilder('a')
            ->where('a.applications IS EMPTY')
            ->andWhere(" a.updatedAt < :dateNow ")
            ->setParameter('dateNow', $dateNow);

        $query = $queryBuilder->getQuery();
        $results = $query->getResult();
        return $results;
    }

    //suppression des categories associées à l'entité
    public function removeCategoriesbyAdvert($em, $advert)
    {
        // On boucle sur les catégories de l'annonce pour les supprimer
        foreach ($advert->getCategories() as $category) {
            $advert->removeCategory($category);
        }
        $em->flush();
    }

    //suppression des categories associées à l'entité
    public function removeSkillsbyAdvert($em, $advert)
    {
        $query = $em->createQuery('DELETE FROM App\Entity\AdvertSkill ad where ad.advert = ' .  $advert->getId());
        $result = $query->getResult();
    }

//********************************** */


    //on fait appel au createQueryBuilder,cette fonction sera appeler dans un controler
    //renvoi toutes les données de la table advert
    public function myFindAll()
    {
        return $this
            ->createQueryBuilder('a')
            ->getQuery()
            ->getResult();
    }

    //correspond au find($id), 
    //ds ce cas on utilise createQueryBuilder
    public function myFindOne($id)
    {
        $qb = $this->createQueryBuilder('a');

        $qb
            ->where('a.id = :id')
            ->setParameter('id', $id);

        return $qb
            ->getQuery()
            ->getResult();
    }

    //méthode pour récupérer toutes les annonces écrites par un auteur avant une année donnée 
    public function findByAuthorAndDate($author, $year)
    {
        $qb = $this->createQueryBuilder('a');

        $qb->where('a.author = :author')
            ->setParameter('author', $author)
            ->andWhere('a.date < :year')
            ->setParameter('year', $year)
            ->orderBy('a.date', 'DESC');

        return $qb
            ->getQuery()
            ->getResult();
    }

    //voyons un des avantages du QueryBuilder
    //la condition « annonces postées durant l'année en cours » est une condition dont on va se resservir souvent. Il faut donc en faire une méthode
    //cette méthode ne traite pas une Query, mais bien uniquement le QueryBuilder. 
    public function whereCurrentYear(QueryBuilder $qb)
    {
        $qb
            ->andWhere('a.date BETWEEN :start AND :end')
            ->setParameter('start', new \Datetime(date('Y') . '-01-01'))  // Date entre le 1er janvier de cette année
            ->setParameter('end',   new \Datetime(date('Y') . '-12-31'))  // Et le 31 décembre de cette année
        ;
    }
    //pour utiliser la méthode whereCurrentYear
    public function myFind()
    {
        $qb = $this->createQueryBuilder('a');

        // On peut ajouter ce qu'on veut avant
        $qb
            ->where('a.author = :author')
            ->setParameter('author', 'Marine');

        // On applique notre condition sur le QueryBuilder
        $this->whereCurrentYear($qb);

        // On peut ajouter ce qu'on veut après
        $qb->orderBy('a.date', 'DESC');

        return $qb
            ->getQuery()
            ->getResult();
    }
// D'abord on crée une jointure avec la méthodeleftJoin() (ou  innerJoin() pour faire l'équivalent d'unINNER JOIN). 
//Le premier argument de la méthode est l'attribut de l'entité principale (celle qui est dans leFROM de la requête) sur lequel faire la jointure. 
//Dans l'exemple, l'entitéAdvert possède un attributapplications. Le deuxième argument de la méthode est l'alias de l'entité jointe (arbitraire).
// Puis on sélectionne également l'entité jointe, via unaddSelect(). 
//En effet, unselect('app') tout court aurait écrasé leselect('a') déjà fait par lecreateQueryBuilder(), rappelez-vous.
    public function getAdvertWithApplications()
    {
        $qb = $this
            ->createQueryBuilder('a')
            ->leftJoin('a.applications', 'app')
            ->addSelect('app');

        return $qb
            ->getQuery()
            ->getResult();
    }

    //  cette fonction va retourner un tableau d'Advert. Qu'est-ce que l'on veut en faire ? Les afficher.
    //par exemple : $repository->getAdvertWithCategories(array('Développeur', 'Intégrateur'));
    public function getAdvertWithCategories(array $categoryNames)
    {
        $qb = $this->createQueryBuilder('a');

        // On fait une jointure avec l'entité Category avec pour alias « c »
        $qb
            ->innerJoin('a.categories', 'c')
            ->addSelect('c');

        // Puis on filtre sur le nom des catégories à l'aide d'un IN
        $qb->where($qb->expr()->in('c.name', $categoryNames));
        // La syntaxe du IN et d'autres expressions se trouve dans la documentation Doctrine

        // Enfin, on retourne le résultat
        return $qb
            ->getQuery()
            ->getResult();
    }

    //Recuperation des entités triées par date
    //avec son image, ses catégories
    public function getAdverts($page, $nbPerPage)
    {
        $query = $this->createQueryBuilder('a')
            // Jointure sur l'attribut image
            ->leftJoin('a.image', 'i')
            ->addSelect('i')
            // Jointure sur l'attribut categories
            ->leftJoin('a.categories', 'c')
            ->addSelect('c')
            ->orderBy('a.date', 'DESC')
            ->getQuery();
        $query
            // On définit l'annonce à partir de laquelle commencer la liste
            ->setFirstResult(($page - 1) * $nbPerPage)
            // Ainsi que le nombre d'annonce à afficher sur une page
            ->setMaxResults($nbPerPage);

        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        // (n'oubliez pas le use correspondant en début de fichier)
        return new Paginator($query, true);
    }


    public function isFlood($ip, $seconds)
    {
        return (bool) $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->where('a.date >= :date')
            ->setParameter('date', new \Datetime($seconds . ' seconds ago'))
        // Nous n'avons pas cet attribut, je laisse en commentaire, mais voici comment pourrait être la condition :
        //->andWhere('a.ip = :ip')->setParameter('ip', $ip)
            ->getQuery()
            ->getSingleScalarResult()
        ; 
    }


    // /**
    //  * @return Advert[] Returns an array of Advert objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Advert
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
