<?php

namespace App\Repository;

use App\Entity\Application;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Application|null find($id, $lockMode = null, $lockVersion = null)
 * @method Application|null findOneBy(array $criteria, array $orderBy = null)
 * @method Application[]    findAll()
 * @method Application[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Application::class);
    }

    public function getApplicationsWithAdvert($limit)
    {
        $qb = $this->createQueryBuilder('a');

        // On fait une jointure avec l'entité Advert avec pour alias « adv »
        $qb
            ->innerJoin('a.advert', 'adv')
            ->addSelect('adv');

        // Puis on ne retourne que $limit résultats
        $qb->setMaxResults($limit);

        // Enfin, on retourne le résultat
        return $qb
            ->getQuery()
            ->getResult();
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
    //  * @return Application[] Returns an array of Application objects
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
    public function findOneBySomeField($value): ?Application
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
