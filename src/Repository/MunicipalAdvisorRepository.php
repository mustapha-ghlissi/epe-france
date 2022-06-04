<?php

namespace App\Repository;

use App\Entity\MunicipalAdvisor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MunicipalAdvisor|null find($id, $lockMode = null, $lockVersion = null)
 * @method MunicipalAdvisor|null findOneBy(array $criteria, array $orderBy = null)
 * @method MunicipalAdvisor[]    findAll()
 * @method MunicipalAdvisor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MunicipalAdvisorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MunicipalAdvisor::class);
    }

    /**
     * @return \Doctrine\DBAL\Cache\ArrayStatement|\Doctrine\DBAL\Cache\ResultCacheStatement|\Doctrine\DBAL\Driver\ResultStatement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resetTable() {
        $qb = $this->_em->createQueryBuilder();

        // Delete all data
        $qb->delete()
            ->from($this->_entityName, 'ma')
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
        $query = $qb->select('COUNT(ma.id)')
            ->from($this->_entityName, 'ma')
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * @param int $limit
     * @return int|mixed|string
     */
    public function getNotNoted(int $limit) {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('ma.id, ma.firstName, ma.lastName, ma.communeLabel')
            ->from($this->_entityName, 'ma')
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
        $query = $qb->select('ma.id, ma.firstName, ma.lastName, ma.communeLabel')
            ->from($this->_entityName, 'ma')
            ->where('ma.firstName LIKE :criteria')
            ->orWhere('ma.lastName LIKE :criteria')
            ->orWhere('ma.communeLabel LIKE :criteria')
            ->orWhere('CONCAT(ma.firstName, \' \' , ma.lastName) LIKE :criteria')
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
        $query = $qb->select('ma')
            ->from($this->_entityName, 'ma')
            ->where('ma.firstName LIKE :firstName')
            ->andWhere('ma.lastName LIKE :lastName')
            ->setParameters($params)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @return int|mixed|string
     */
    public function getMembersByDepartment() {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('COUNT(ma.id) as count, ma.departmentLabel')
            ->from($this->_entityName, 'ma')
            ->groupBy('ma.departmentLabel')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function getMembersByArea() {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('COUNT(ma.id) as count, ma.areaLabel')
            ->from($this->_entityName, 'ma')
            ->groupBy('ma.areaLabel')
            ->getQuery();

        return $query->getResult();
    }
}
