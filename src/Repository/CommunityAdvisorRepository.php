<?php

namespace App\Repository;

use App\Entity\CommunityAdvisor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommunityAdvisor|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommunityAdvisor|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommunityAdvisor[]    findAll()
 * @method CommunityAdvisor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommunityAdvisorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommunityAdvisor::class);
    }

    /**
     * @return \Doctrine\DBAL\Cache\ArrayStatement|\Doctrine\DBAL\Cache\ResultCacheStatement|\Doctrine\DBAL\Driver\ResultStatement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resetTable() {
        $qb = $this->_em->createQueryBuilder();

        // Delete all data
        $qb->delete()
            ->from($this->_entityName, 'ca')
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
        $query = $qb->select('COUNT(ca.id)')
            ->from($this->_entityName, 'ca')
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * @param int $limit
     * @return int|mixed|string
     */
    public function getNotNoted(int $limit) {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('ca.id, ca.firstName, ca.lastName, ca.communeLabel')
            ->from($this->_entityName, 'ca')
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
        $query = $qb->select('ca.id, ca.firstName, ca.lastName, ca.communeLabel')
            ->from($this->_entityName, 'ca')
            ->where('ca.firstName LIKE :criteria')
            ->orWhere('ca.lastName LIKE :criteria')
            ->orWhere('ca.communeLabel LIKE :criteria')
            ->orWhere('CONCAT(ca.firstName, \' \' , ca.lastName) LIKE :criteria')
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
        $query = $qb->select('ca')
            ->from($this->_entityName, 'ca')
            ->where('ca.firstName LIKE :firstName')
            ->andWhere('ca.lastName LIKE :lastName')
            ->setParameters($params)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @return int|mixed|string
     */
    public function getMembersByDepartment() {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('COUNT(ca.id) as count, ca.departmentLabel')
            ->from($this->_entityName, 'ca')
            ->groupBy('ca.departmentLabel')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function getMembersByArea() {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('COUNT(ca.id) as count, ca.areaLabel')
            ->from($this->_entityName, 'ca')
            ->groupBy('ca.areaLabel')
            ->getQuery();

        return $query->getResult();
    }
}
