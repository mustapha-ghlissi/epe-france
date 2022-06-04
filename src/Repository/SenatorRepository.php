<?php

namespace App\Repository;

use App\Entity\Senator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Senator|null find($id, $lockMode = null, $lockVersion = null)
 * @method Senator|null findOneBy(array $criteria, array $orderBy = null)
 * @method Senator[]    findAll()
 * @method Senator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SenatorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Senator::class);
    }

    /**
     * @return \Doctrine\DBAL\Cache\ArrayStatement|\Doctrine\DBAL\Cache\ResultCacheStatement|\Doctrine\DBAL\Driver\ResultStatement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resetTable() {
        $qb = $this->_em->createQueryBuilder();

        // Delete all data
        $qb->delete()
            ->from($this->_entityName, 's')
            ->getQuery()
            ->execute();

        // Reset table AutoIncrement to 1
        $table = $this->getClassMetadata()->getTableName();
        $con = $this->_em->getConnection();
        return $con->executeQuery("ALTER TABLE {$table} AUTO_INCREMENT = 1");
    }


    /**
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCount() {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('COUNT(s.id)')
            ->from($this->_entityName, 's')
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * @param int $limit
     * @return int|mixed|string
     */
    public function getNotNoted(int $limit) {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('s.id, s.firstName, s.lastName, s.departmentLabel')
            ->from($this->_entityName, 's')
            ->setMaxResults($limit)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param string $criteria
     * @return int|mixed|string
     */
    public function getByCriteria(string $criteria) {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('s.id, s.firstName, s.lastName, s.departmentLabel')
            ->from($this->_entityName, 's')
            ->where('s.firstName LIKE :criteria')
            ->orWhere('s.lastName LIKE :criteria')
            ->orWhere('s.departmentLabel LIKE :criteria')
            ->orWhere('CONCAT(s.firstName, \' \' , s.lastName) LIKE :criteria')
            ->setParameter('criteria', '%' . $criteria . '%')
            ->setMaxResults(20)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param array $params
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getByName(array $params) {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('s')
            ->from($this->_entityName, 's')
            ->where('s.firstName LIKE :firstName')
            ->andWhere('s.lastName LIKE :lastName')
            ->setParameters($params)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @return int|mixed|string
     */
    public function getMembersByDepartment() {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('COUNT(s.id) as count, s.departmentLabel')
            ->from($this->_entityName, 's')
            ->groupBy('s.departmentLabel')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function getMembersByArea() {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('COUNT(s.id) as count, s.areaLabel')
            ->from($this->_entityName, 's')
            ->groupBy('s.areaLabel')
            ->getQuery();

        return $query->getResult();
    }
}
