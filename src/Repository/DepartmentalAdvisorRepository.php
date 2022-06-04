<?php

namespace App\Repository;

use App\Entity\DepartmentalAdvisor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DepartmentalAdvisor|null find($id, $lockMode = null, $lockVersion = null)
 * @method DepartmentalAdvisor|null findOneBy(array $criteria, array $orderBy = null)
 * @method DepartmentalAdvisor[]    findAll()
 * @method DepartmentalAdvisor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepartmentalAdvisorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DepartmentalAdvisor::class);
    }

    /**
     * @return \Doctrine\DBAL\Cache\ArrayStatement|\Doctrine\DBAL\Cache\ResultCacheStatement|\Doctrine\DBAL\Driver\ResultStatement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resetTable() {
        $qb = $this->_em->createQueryBuilder();

        // Delete all data
        $qb->delete()
            ->from($this->_entityName, 'da')
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
        $query = $qb->select('COUNT(da.id)')
            ->from($this->_entityName, 'da')
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPresidentsCount() {
        $label = 'Président du conseil départemental';
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('COUNT(pd.id)')
            ->from($this->_entityName, 'pd')
            ->where('pd.functionLabel = :label')
           ->setParameter('label', $label)
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * @param int $limit
     * @return int|mixed|string
     */
    public function getPresidents(int $limit) {
        $label = 'Président du conseil départemental';
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('dp.id, dp.firstName, dp.lastName, dp.departmentLabel')
            ->from($this->_entityName, 'dp')
            ->where('dp.functionLabel = :label')
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
        $query = $qb->select('da.id, da.firstName, da.lastName, da.departmentLabel')
            ->from($this->_entityName, 'da')
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
        $query = $qb->select('da.id, da.firstName, da.lastName, da.departmentLabel')
            ->from($this->_entityName, 'da')
            ->where('da.firstName LIKE :criteria')
            ->orWhere('da.lastName LIKE :criteria')
            ->orWhere('da.departmentLabel LIKE :criteria')
            ->orWhere('da.departmentCode LIKE :criteria')
            ->orWhere('CONCAT(da.firstName, \' \' , da.lastName) LIKE :criteria')
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
        $label = 'Président du conseil départemental';
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('dp.id, dp.firstName, dp.lastName, dp.departmentLabel')
            ->from($this->_entityName, 'dp')
            ->where('dp.firstName LIKE :criteria')
            ->orWhere('dp.lastName LIKE :criteria')
            ->orWhere('dp.departmentLabel LIKE :criteria')
            ->orWhere('dp.departmentCode LIKE :criteria')
            ->orWhere('CONCAT(dp.firstName, \' \' , dp.lastName) LIKE :criteria')
            ->andWhere('dp.functionLabel = :label')
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
        $query = $qb->select('da')
            ->from($this->_entityName, 'da')
            ->where('da.firstName LIKE :firstName')
            ->andWhere('da.lastName LIKE :lastName')
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
        $query = $qb->select('da')
            ->from($this->_entityName, 'da')
            ->where('da.firstName LIKE :firstName')
            ->andWhere('da.lastName LIKE :lastName')
            ->andWhere('da.functionLabel = :functionLabel')
            ->setParameters($params)
            ->getQuery();

        return $query->getOneOrNullResult();
    }


    /**
     * @return int|mixed|string
     */
    public function getMembersByDepartment() {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('COUNT(da.id) as count, da.departmentLabel')
            ->from($this->_entityName, 'da')
            ->groupBy('da.departmentLabel')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function getMembersByArea() {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('COUNT(da.id) as count, da.areaLabel')
            ->from($this->_entityName, 'da')
            ->groupBy('da.areaLabel')
            ->getQuery();

        return $query->getResult();
    }
}
