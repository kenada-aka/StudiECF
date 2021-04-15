<?php

namespace App\Repository;

use App\Entity\Realty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use Doctrine\ORM\Tools\Pagination\Paginator;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Realty|null find($id, $lockMode = null, $lockVersion = null)
 * @method Realty|null findOneBy(array $criteria, array $orderBy = null)
 * @method Realty[]    findAll()
 * @method Realty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RealtyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Realty::class);
    }

    /**
     * Récupère la liste des biens qui ne sont pas louer
     **/
    public function findAllFreeRent()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.statut = :val')
            ->setParameter('val', 3)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère la location par le locataire
     **/
    public function findByTenant(int $tenantId)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id_tenant = :val')
            ->setParameter('val', $tenantId)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère la liste des biens par rapport au propriétaire
     **/
    public function findAllWhereOwnerId(int $ownerId)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id_owner = :val')
            ->setParameter('val', $ownerId)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère la liste des biens par rapport à l'agence
     **/
    public function findAllWhereAgencyId(int $agencyId)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id_agency = :val')
            ->setParameter('val', $agencyId)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère la liste des biens par rapport au propriétaire (en tant qu'Agence)
     **/
    public function findAllWhereOwnerExtends()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.statut = :val')
            ->setParameter('val', 1)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère une liste d'articles triés et paginés.
     *
     * @param int $page Le numéro de la page
     * @param int $nbMaxParPage Nombre maximum d'article par page     
     *
     * @throws InvalidArgumentException
     * @throws NotFoundHttpException
     *
     * @return Paginator
     */
    public function findAllWithPagination($page, $nbMaxParPage, $order)
    {
        if(!is_numeric($page))
        {
            throw new InvalidArgumentException("La valeur de l'argument $page est incorrecte (valeur : " . $page . ").");
        }

        if($page < 1)
        {
            throw new NotFoundHttpException("La page demandée n'existe pas");
        }

        if(!is_numeric($nbMaxParPage))
        {
            throw new InvalidArgumentException("La valeur de l'argument $nbMaxParPage est incorrecte (valeur : " . $nbMaxParPage . ").");
        }
    
        $qb = $this->createQueryBuilder('a')
            ->orderBy('a.rent', $order);
        
        $query = $qb->getQuery();

        $premierResultat = ($page - 1) * $nbMaxParPage;
        $query->setFirstResult($premierResultat)->setMaxResults($nbMaxParPage);
        $paginator = new Paginator($query);

        if(($paginator->count() <= $premierResultat) && $page != 1)
        {
            throw new NotFoundHttpException("La page demandée n'existe pas.");
        }

        return $paginator;
    }

    // /**
    //  * @return Realty[] Returns an array of Realty objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Realty
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
