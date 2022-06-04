<?php

namespace App\Repository;

use App\Entity\Mayor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Mayor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mayor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mayor[]    findAll()
 * @method Mayor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MayorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mayor::class);
    }

    /**
     * @return \Doctrine\DBAL\Cache\ArrayStatement|\Doctrine\DBAL\Cache\ResultCacheStatement|\Doctrine\DBAL\Driver\ResultStatement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resetTable() {
        $qb = $this->_em->createQueryBuilder();

        // Delete all data
        $qb->delete()
            ->from($this->_entityName, 'm')
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
        $query = $qb->select('COUNT(m.id)')
            ->from($this->_entityName, 'm')
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * @param int $limit
     * @return int|mixed|string
     */
    public function getNotNoted(int $limit) {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('m.id, m.firstName, m.lastName, m.communeLabel')
            ->from($this->_entityName, 'm')
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
        $query = $qb->select('m.id, m.firstName, m.lastName, m.communeLabel')
            ->from($this->_entityName, 'm')
            ->where('m.firstName LIKE :criteria')
            ->orWhere('m.lastName LIKE :criteria')
            ->orWhere('m.communeLabel LIKE :criteria')
            ->orWhere('CONCAT(m.firstName, \' \' , m.lastName) LIKE :criteria')
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
        $query = $qb->select('m')
            ->from($this->_entityName, 'm')
            ->where('m.firstName LIKE :firstName')
            ->andWhere('m.lastName LIKE :lastName')
            ->setParameters($params)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @return int|mixed|string
     */
    public function getMembersByDepartment() {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('COUNT(m.id) as count, m.departmentLabel')
            ->from($this->_entityName, 'm')
            ->groupBy('m.departmentLabel')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function getMembersByArea() {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('COUNT(m.id) as count, m.areaLabel')
            ->from($this->_entityName, 'm')
            ->groupBy('m.areaLabel')
            ->getQuery();

        return $query->getResult();
    }
}
