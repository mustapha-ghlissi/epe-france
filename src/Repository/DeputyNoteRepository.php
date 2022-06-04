<?php

namespace App\Repository;

use App\Entity\DeputyNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeputyNote|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeputyNote|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeputyNote[]    findAll()
 * @method DeputyNote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeputyNoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeputyNote::class);
    }


    /**
     * @return \Doctrine\DBAL\Cache\ArrayStatement|\Doctrine\DBAL\Cache\ResultCacheStatement|\Doctrine\DBAL\Driver\ResultStatement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resetTable() {
        $qb = $this->_em->createQueryBuilder();

        // Delete all data
        $qb->delete()
            ->from($this->_entityName, 'dn')
            ->getQuery()
            ->execute();

        // Reset table AutoIncrement to 1
        $table = $this->getClassMetadata()->getTableName();
        $con = $this->_em->getConnection();
        return $con->executeQuery("ALTER TABLE {$table} AUTO_INCREMENT = 1");
    }

    /**
     * @return int|mixed|string
     */
    public function getDeputies() {

        $qb = $this->_em->createQueryBuilder();

        $dql = "COUNT(d.id) as countNotes, d.id, d.firstName, d.lastName,d.departmentLabel, ";
        $dql .= "((SUM(n.presenceNumber + n.amendmentsNumber + n.votesNumber + n.participationsNumber + ";
        $dql .= "n.suggestionsNumber + n.reportsNumber + n.questionsNumber))/COUNT(d.id))/7 as note";

        $query = $qb->select($dql)
            ->from($this->_entityName, 'n')
            ->innerJoin('n.deputy', 'd')
            ->groupBy('d.id')
            ->orderBy('note', 'DESC')
            ->setMaxResults(10)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCount() {
        return $this->createQueryBuilder('n')
            ->select('COUNT(n.id) as nbNotes')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
