<?php

namespace App\Repository;

use App\Entity\BoardMinute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BoardMinute|null find($id, $lockMode = null, $lockVersion = null)
 * @method BoardMinute|null findOneBy(array $criteria, array $orderBy = null)
 * @method BoardMinute[]    findAll()
 * @method BoardMinute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoardMinuteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BoardMinute::class);
    }

    public function getByCommune($code, $name) {

        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('b')
            ->from($this->_entityName, 'b')
            ->join('b.commune', 'c')
            ->where('c.codeInsee = :code')
            ->andWhere('c.name LIKE :name')
            ->setParameter('code', $code)
            ->setParameter('name', '%' . $name . '%')
            ->getQuery();

        return $query->getResult();
    }

    public function getByDepartment($name) {

        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('b')
            ->from($this->_entityName, 'b')
            ->join('b.department', 'd')
            ->where('d.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery();

        return $query->getResult();
    }

    public function getByArea($name) {

        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('b')
            ->from($this->_entityName, 'b')
            ->join('b.area', 'a')
            ->where('a.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery();

        return $query->getResult();
    }
}
