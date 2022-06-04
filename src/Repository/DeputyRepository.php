<?php

namespace App\Repository;

use App\Entity\Deputy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Deputy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Deputy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Deputy[]    findAll()
 * @method Deputy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeputyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deputy::class);
    }

    /**
     * @return \Doctrine\DBAL\Cache\ArrayStatement|\Doctrine\DBAL\Cache\ResultCacheStatement|\Doctrine\DBAL\Driver\ResultStatement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resetTable() {
        $qb = $this->_em->createQueryBuilder();

        // Delete all data
        $qb->delete()
            ->from($this->_entityName, 'd')
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
        $query = $qb->select('COUNT(d.id)')
            ->from($this->_entityName, 'd')
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * @param int $limit
     * @return int|mixed|string
     */
    public function getNotNoted(int $limit) {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('d.id, d.firstName, d.lastName, d.departmentLabel')
            ->from($this->_entityName, 'd')
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
        $query = $qb->select('d.id, d.firstName, d.lastName, d.departmentLabel')
            ->from($this->_entityName, 'd')
            ->where('d.firstName LIKE :criteria')
            ->orWhere('d.lastName LIKE :criteria')
            ->orWhere('d.departmentLabel LIKE :criteria')
            ->orWhere('CONCAT(d.firstName, \' \' , d.lastName) LIKE :criteria')
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
        $query = $qb->select('d')
            ->from($this->_entityName, 'd')
            ->where('d.firstName LIKE :firstName')
            ->andWhere('d.lastName LIKE :lastName')
            ->setParameters($params)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @return int|mixed|string
     */
    public function getMembersByDepartment() {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('COUNT(d.id) as count, d.departmentLabel')
            ->from($this->_entityName, 'd')
            ->groupBy('d.departmentLabel')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function getMembersByArea() {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('COUNT(d.id) as count, d.areaLabel')
            ->from($this->_entityName, 'd')
            ->groupBy('d.areaLabel')
            ->getQuery();

        return $query->getResult();
    }
}
