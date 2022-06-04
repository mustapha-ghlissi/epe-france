<?php

namespace App\Repository;

use App\Entity\EuroDeputyNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EuroDeputyNote|null find($id, $lockMode = null, $lockVersion = null)
 * @method EuroDeputyNote|null findOneBy(array $criteria, array $orderBy = null)
 * @method EuroDeputyNote[]    findAll()
 * @method EuroDeputyNote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EuroDeputyNoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EuroDeputyNote::class);
    }


    /**
     * @return \Doctrine\DBAL\Cache\ArrayStatement|\Doctrine\DBAL\Cache\ResultCacheStatement|\Doctrine\DBAL\Driver\ResultStatement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resetTable() {
        $qb = $this->_em->createQueryBuilder();

        // Delete all data
        $qb->delete()
            ->from($this->_entityName, 'edn')
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

        $dql = "COUNT(ed.id) as countNotes, ed.id, ed.firstName, ed.lastName,ed.professionLabel, ";
        $dql .= "((SUM(n.physicalPresence + n.amendmentsNumber + n.votesNumber + n.participationsNumber + ";
        $dql .= "n.suggestionsNumber + n.questionsNumber))/COUNT(ed.id))/6 as note";

        $query = $qb->select($dql)
            ->from($this->_entityName, 'n')
            ->innerJoin('n.euroDeputy', 'ed')
            ->groupBy('ed.id')
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
