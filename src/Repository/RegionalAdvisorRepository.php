<?php

namespace App\Repository;

use App\Entity\RegionalAdvisor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RegionalAdvisor|null find($id, $lockMode = null, $lockVersion = null)
 * @method RegionalAdvisor|null findOneBy(array $criteria, array $orderBy = null)
 * @method RegionalAdvisor[]    findAll()
 * @method RegionalAdvisor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegionalAdvisorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegionalAdvisor::class);
    }

    /**
     * @return \Doctrine\DBAL\Cache\ArrayStatement|\Doctrine\DBAL\Cache\ResultCacheStatement|\Doctrine\DBAL\Driver\ResultStatement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resetTable() {
        $qb = $this->_em->createQueryBuilder();

        // Delete all data
        $qb->delete()
            ->from($this->_entityName, 'ra')
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
        $query = $qb->select('COUNT(ra.id)')
            ->from($this->_entityName, 'ra')
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPresidentsCount() {
        $label = 'Président du conseil régional';
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('COUNT(pr.id)')
            ->from($this->_entityName, 'pr')
            ->where('pr.functionLabel = :label')
            ->setParameter('label', $label)
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * @param int $limit
     * @return int|mixed|string
     */
    public function getPresidents(int $limit) {
        $label = 'Président du conseil régional';
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('rp.id, rp.firstName, rp.lastName, rp.areaLabel')
            ->from($this->_entityName, 'rp')
            ->where('rp.functionLabel = :label')
            ->setParameter('label', $label)
            ->setMaxResults($limit)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param int $limit
     * @return int|mixed|string
     */
    public function getNotNoted(int $limit) {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('ra.id, ra.firstName, ra.lastName, ra.areaLabel')
            ->from($this->_entityName, 'ra')
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
        $query = $qb->select('ra.id, ra.firstName, ra.lastName, ra.areaLabel')
            ->from($this->_entityName, 'ra')
            ->where('ra.firstName LIKE :criteria')
            ->orWhere('ra.lastName LIKE :criteria')
            ->orWhere('ra.areaLabel LIKE :criteria')
            ->orWhere('CONCAT(ra.firstName, \' \' , ra.lastName) LIKE :criteria')
            ->setParameter('criteria', '%' . $criteria . '%')
            ->setMaxResults(20)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param string $criteria
     * @return int|mixed|string
     */
    public function getPresidentsByCriteria(string $criteria) {
        $label = 'Président du conseil régional';
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('rp.id, rp.firstName, rp.lastName, rp.areaLabel')
            ->from($this->_entityName, 'rp')
            ->where('rp.firstName LIKE :criteria')
            ->orWhere('rp.lastName LIKE :criteria')
            ->orWhere('rp.areaLabel LIKE :criteria')
            ->orWhere('CONCAT(rp.firstName, \' \' , rp.lastName) LIKE :criteria')
            ->andWhere('rp.functionLabel = :label')
            ->setParameter('criteria', '%' . $criteria . '%')
            ->setParameter('label', $label)
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
        $query = $qb->select('ra')
            ->from($this->_entityName, 'ra')
            ->where('ra.firstName LIKE :firstName')
            ->andWhere('ra.lastName LIKE :lastName')
            ->setParameters($params)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @param array $params
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPresidentByName(array $params) {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('ra')
            ->from($this->_entityName, 'ra')
            ->where('ra.firstName LIKE :firstName')
            ->andWhere('ra.lastName LIKE :lastName')
            ->andWhere('ra.functionLabel = :functionLabel')
            ->setParameters($params)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @return int|mixed|string
     */
    public function getMembersByDepartment() {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('COUNT(ra.id) as count, ra.departmentLabel')
            ->from($this->_entityName, 'ra')
            ->groupBy('ra.departmentLabel')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function getMembersByArea() {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('COUNT(ra.id) as count, ra.areaLabel')
            ->from($this->_entityName, 'ra')
            ->groupBy('ra.areaLabel')
            ->getQuery();

        return $query->getResult();
    }
}
