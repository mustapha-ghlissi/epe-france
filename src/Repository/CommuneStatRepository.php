<?php

namespace App\Repository;

use App\Entity\CommuneStat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommuneStat|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommuneStat|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommuneStat[]    findAll()
 * @method CommuneStat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommuneStatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommuneStat::class);
    }

    /**
     * @param $areaLabel
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getByArea($areaLabel)
    {
        return $this->createQueryBuilder('cs')
            ->select('SUM(cs.nbCommunes) as nbCommunes')
            ->where('cs.areaLabel LIKE :label')
            ->setParameter('label', '%' . $areaLabel . '%')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @param $depLabel
     * @return int|mixed|string
     */
    public function getByDepartment($depLabel)
    {
        return $this->createQueryBuilder('cs')
            ->select('cs.nbCommunes')
            ->where('cs.departmentLabel LIKE :label')
            ->setParameter('label', '%' . $depLabel . '%')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
