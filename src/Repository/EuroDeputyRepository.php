<?php

namespace App\Repository;

use App\Entity\EuroDeputy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EuroDeputy|null find($id, $lockMode = null, $lockVersion = null)
 * @method EuroDeputy|null findOneBy(array $criteria, array $orderBy = null)
 * @method EuroDeputy[]    findAll()
 * @method EuroDeputy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EuroDeputyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EuroDeputy::class);
    }

    /**
     * @return \Doctrine\DBAL\Cache\ArrayStatement|\Doctrine\DBAL\Cache\ResultCacheStatement|\Doctrine\DBAL\Driver\ResultStatement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resetTable() {
        $qb = $this->_em->createQueryBuilder();

        // Delete all data
        $qb->delete()
            ->from($this->_entityName, 'ed')
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
        $query = $qb->select('COUNT(ed.id)')
            ->from($this->_entityName, 'ed')
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * @param int $limit
     * @return int|mixed|string
     */
    public function getNotNoted(int $limit) {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('ed.id, ed.firstName, ed.lastName')
            ->from($this->_entityName, 'ed')
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
        $query = $qb->select('ed.id, ed.firstName, ed.lastName')
            ->from($this->_entityName, 'ed')
            ->where('ed.firstName LIKE :criteria')
            ->orWhere('ed.lastName LIKE :criteria')
            ->orWhere('CONCAT(ed.firstName, \' \' , ed.lastName) LIKE :criteria')
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
        $query = $qb->select('ed')
            ->from($this->_entityName, 'ed')
            ->where('ed.firstName LIKE :firstName')
            ->andWhere('ed.lastName LIKE :lastName')
            ->setParameters($params)
            ->getQuery();

        return $query->getOneOrNullResult();
    }
}
