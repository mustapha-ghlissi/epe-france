<?php

namespace App\Repository;

use App\Entity\MPDPRNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MPDPRNote|null find($id, $lockMode = null, $lockVersion = null)
 * @method MPDPRNote|null findOneBy(array $criteria, array $orderBy = null)
 * @method MPDPRNote[]    findAll()
 * @method MPDPRNote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MPDPRNoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MPDPRNote::class);
    }

    /**
     * @return \Doctrine\DBAL\Cache\ArrayStatement|\Doctrine\DBAL\Cache\ResultCacheStatement|\Doctrine\DBAL\Driver\ResultStatement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resetTable() {
        $qb = $this->_em->createQueryBuilder();

        // Delete all data
        $qb->delete()
            ->from($this->_entityName, 'mpdprn')
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
    public function getDepartmentalPresidents() {

        $qb = $this->_em->createQueryBuilder();

        $dql = "COUNT(pd.id) as countNotes, pd.id, pd.firstName, pd.lastName,pd.departmentLabel, ";
        $dql .= "((SUM(n.security + n.socialAction + n.jobProfessionalInsert + n.teaching + ";
        $dql .= "n.youthChildhood + n.sports + n.economicalIntervention + n.cityPolitics + ";
        $dql .= "n.ruralDevelopment + n.accommodation + n.environment + n.garbage + ";
        $dql .= "n.telecoms + n.energy + n.transports))/COUNT(pd.id))/15 as note";

        $query = $qb->select($dql)
            ->from($this->_entityName, 'n')
            ->innerJoin('n.departmentalPresident', 'pd')
            ->groupBy('pd.id')
            ->orderBy('note', 'DESC')
            ->setMaxResults(10)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function getRegionalPresidents() {

        $qb = $this->_em->createQueryBuilder();

        $dql = "COUNT(pr.id) as countNotes, pr.id, pr.firstName, pr.lastName, pr.areaLabel, ";
        $dql .= "((SUM(n.security + n.socialAction + n.jobProfessionalInsert + n.teaching + ";
        $dql .= "n.youthChildhood + n.sports + n.economicalIntervention + n.cityPolitics + ";
        $dql .= "n.ruralDevelopment + n.accommodation + n.environment + n.garbage + ";
        $dql .= "n.telecoms + n.energy + n.transports))/COUNT(pr.id))/15 as note";

        $query = $qb->select($dql)
            ->from($this->_entityName, 'n')
            ->innerJoin('n.regionalPresident', 'pr')
            ->groupBy('pr.id')
            ->orderBy('note', 'DESC')
            ->setMaxResults(10)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return int|mixed|string
     */
    public function getMayors() {

        $qb = $this->_em->createQueryBuilder();

        $dql = "COUNT(m.id) as countNotes, m.id, m.firstName, m.lastName,m.communeLabel, ";
        $dql .= "((SUM(n.security + n.socialAction + n.jobProfessionalInsert + n.teaching + ";
        $dql .= "n.youthChildhood + n.sports + n.economicalIntervention + n.cityPolitics + ";
        $dql .= "n.ruralDevelopment + n.accommodation + n.environment + n.garbage + ";
        $dql .= "n.telecoms + n.energy + n.transports))/COUNT(m.id))/15 as note";

        $query = $qb->select($dql)
            ->from($this->_entityName, 'n')
            ->innerJoin('n.mayor', 'm')
            ->groupBy('m.id')
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
