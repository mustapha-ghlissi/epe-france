<?php

namespace App\Repository;

use App\Entity\OtherNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OtherNote|null find($id, $lockMode = null, $lockVersion = null)
 * @method OtherNote|null findOneBy(array $criteria, array $orderBy = null)
 * @method OtherNote[]    findAll()
 * @method OtherNote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OtherNoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OtherNote::class);
    }

    /**
     * @return \Doctrine\DBAL\Cache\ArrayStatement|\Doctrine\DBAL\Cache\ResultCacheStatement|\Doctrine\DBAL\Driver\ResultStatement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resetTable() {
        $qb = $this->_em->createQueryBuilder();

        // Delete all data
        $qb->delete()
            ->from($this->_entityName, 'on')
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
    public function getMunicipalAdvisors() {

        $qb = $this->_em->createQueryBuilder();

        $dql = "COUNT(ma.id) as countNotes, ma.id, ma.firstName, ma.lastName, ma.communeLabel, ";
        $dql .= "((SUM(n.presenceNumber + n.amendmentsNumber + n.achievementsNumber + ";
        $dql .= "n.worksNumber))/COUNT(ma.id))/4 as note";

        $query = $qb->select($dql)
            ->from($this->_entityName, 'n')
            ->innerJoin('n.municipalAdvisor', 'ma')
            ->groupBy('ma.id')
            ->orderBy('note', 'DESC')
            ->setMaxResults(10)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function getCorsicanAdvisors() {

        $qb = $this->_em->createQueryBuilder();

        $dql = "COUNT(ca.id) as countNotes, ca.id, ca.firstName, ca.lastName, ca.departmentLabel, ";
        $dql .= "((SUM(n.presenceNumber + n.amendmentsNumber + n.achievementsNumber + ";
        $dql .= "n.worksNumber))/COUNT(ca.id))/15 as note";

        $query = $qb->select($dql)
            ->from($this->_entityName, 'n')
            ->innerJoin('n.corsicanAdvisor', 'ca')
            ->groupBy('ca.id')
            ->orderBy('note', 'DESC')
            ->setMaxResults(10)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function getCommunityAdvisors() {

        $qb = $this->_em->createQueryBuilder();

        $dql = "COUNT(ca.id) as countNotes, ca.id, ca.firstName, ca.lastName, ca.communeLabel, ";
        $dql .= "((SUM(n.presenceNumber + n.amendmentsNumber + n.achievementsNumber + ";
        $dql .= "n.worksNumber))/COUNT(ca.id))/15 as note";

        $query = $qb->select($dql)
            ->from($this->_entityName, 'n')
            ->innerJoin('n.communityAdvisor', 'ca')
            ->groupBy('ca.id')
            ->orderBy('note', 'DESC')
            ->setMaxResults(10)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function getDepartmentalAdvisors() {

        $qb = $this->_em->createQueryBuilder();

        $dql = "COUNT(da.id) as countNotes, da.id, da.firstName, da.lastName, da.departmentLabel, ";
        $dql .= "((SUM(n.presenceNumber + n.amendmentsNumber + n.achievementsNumber + ";
        $dql .= "n.worksNumber))/COUNT(da.id))/15 as note";

        $query = $qb->select($dql)
            ->from($this->_entityName, 'n')
            ->innerJoin('n.departmentalAdvisor', 'da')
            ->groupBy('da.id')
            ->orderBy('note', 'DESC')
            ->setMaxResults(10)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function getRegionalAdvisors() {

        $qb = $this->_em->createQueryBuilder();

        $dql = "COUNT(ra.id) as countNotes, ra.id, ra.firstName, ra.lastName, ra.regionLabel, ";
        $dql .= "((SUM(n.presenceNumber + n.amendmentsNumber + n.achievementsNumber + ";
        $dql .= "n.worksNumber))/COUNT(ra.id))/15 as note";

        $query = $qb->select($dql)
            ->from($this->_entityName, 'n')
            ->innerJoin('n.regionalAdvisor', 'ra')
            ->groupBy('ra.id')
            ->orderBy('note', 'DESC')
            ->setMaxResults(10)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function getSenators() {

        $qb = $this->_em->createQueryBuilder();

        $dql = "COUNT(s.id) as countNotes, s.id, s.firstName, s.lastName, s.departmentLabel, ";
        $dql .= "((SUM(n.presenceNumber + n.amendmentsNumber + n.achievementsNumber + ";
        $dql .= "n.worksNumber))/COUNT(s.id))/15 as note";

        $query = $qb->select($dql)
            ->from($this->_entityName, 'n')
            ->innerJoin('n.senator', 's')
            ->groupBy('s.id')
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
