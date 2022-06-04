<?php

namespace App\Repository;

use App\Entity\Tax;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tax|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tax|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tax[]    findAll()
 * @method Tax[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tax::class);
    }

    /**
     * @return \Doctrine\DBAL\Cache\ArrayStatement|\Doctrine\DBAL\Cache\ResultCacheStatement|\Doctrine\DBAL\Driver\ResultStatement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resetTable() {
        $qb = $this->_em->createQueryBuilder();

        // Delete all data
        $qb->delete()
            ->from($this->_entityName, 't')
            ->getQuery()
            ->execute();

        // Reset table AutoIncrement to 1
        $table = $this->getClassMetadata()->getTableName();
        $con = $this->_em->getConnection();
        return $con->executeQuery("ALTER TABLE {$table} AUTO_INCREMENT = 1");
    }

    /**
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getSource() {
        return
        $this->createQueryBuilder('t')
            ->select('t.source')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /*public function getYears($communeLabel, $zipCode) {
        return
            $this->createQueryBuilder('t')
                ->select('DISTINCT t.year')
                ->where('t.communeLabel = :communeLabel')
                ->andWhere('t.zipCode = :zipCode')
                ->setParameter('communeLabel', $communeLabel)
                ->setParameter('zipCode', $zipCode)
                ->getQuery()
                ->getResult();
    }*/

    // Tax commune
    public function getCommuneTaxByCommune($codeInsee) {

        $qb = $this->_em->createQueryBuilder();

        $dql = "DISTINCT t.year, t.codeInsee, t.nbTaxHomes, t.taxRevenue, t.totalAmount, ";
        $dql .= "t.nbImposableTaxHomes, t.imposableTaxRevenue, t.salaryNbTaxHomes, t.salaryTaxRevenue,";
        $dql .= "t.pensionNbTaxHomes, t.pensionTaxRevenue";

        $query = $qb->select($dql)
            ->from($this->_entityName, 't')
            ->where('t.codeInsee = :codeInsee')
            ->setParameter('codeInsee', $codeInsee)
            ->orderBy('t.year', 'DESC')
            ->getQuery();

        return $query->getResult();
    }
    public function getCommuneTaxByDepartment($departmentLabel) {
        $qb = $this->_em->createQueryBuilder();

        $dql = "DISTINCT t.year, t.codeInsee, t.communeLabel, t.nbTaxHomes, t.taxRevenue, t.totalAmount, ";
        $dql .= "t.nbImposableTaxHomes, t.imposableTaxRevenue, t.salaryNbTaxHomes, t.salaryTaxRevenue,";
        $dql .= "t.pensionNbTaxHomes, t.pensionTaxRevenue";

        $query = $qb->select($dql)
            ->from($this->_entityName, 't')
            ->where('t.departmentLabel = :departmentLabel')
            ->setParameter('departmentLabel', $departmentLabel)
            ->orderBy('t.year', 'DESC')
            ->getQuery();

        return $query->getResult();
    }
    public function getCommuneTaxByArea($areaLabel) {
        $qb = $this->_em->createQueryBuilder();

        $dql = "DISTINCT t.year, t.codeInsee, t.communeLabel, t.departmentLabel, t.nbTaxHomes, t.taxRevenue, t.totalAmount, ";
        $dql .= "t.nbImposableTaxHomes, t.imposableTaxRevenue, t.salaryNbTaxHomes, t.salaryTaxRevenue,";
        $dql .= "t.pensionNbTaxHomes, t.pensionTaxRevenue";

        $query = $qb->select($dql)
            ->from($this->_entityName, 't')
            ->where('t.areaLabel = :areaLabel')
            ->setParameter('areaLabel', $areaLabel)
            ->orderBy('t.year', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

    // Tax department
    public function getDepartmentTaxByDepartment($departmentLabel) {
        $qb = $this->_em->createQueryBuilder();
        $dql = "DISTINCT t.year, t.codeInsee, t.communeLabel, t.nbTaxHomes, t.taxRevenue, t.totalAmount, ";
        $dql .= "t.nbImposableTaxHomes, t.imposableTaxRevenue, t.salaryNbTaxHomes, t.salaryTaxRevenue,";
        $dql .= "t.pensionNbTaxHomes, t.pensionTaxRevenue";

        $query = $qb->select($dql)
            ->from($this->_entityName, 't')
            ->where('t.departmentLabel = :departmentLabel')
            ->setParameter('departmentLabel', $departmentLabel)
            ->orderBy('t.year', 'DESC')
            ->getQuery();

        return $query->getResult();
    }
    public function getDepartmentTaxByArea($areaLabel) {

        $qb = $this->_em->createQueryBuilder();

        $dql = "DISTINCT t.year, t.codeInsee, t.communeLabel, t.departmentLabel, t.nbTaxHomes, t.taxRevenue, t.totalAmount, ";
        $dql .= "t.nbImposableTaxHomes, t.imposableTaxRevenue, t.salaryNbTaxHomes, t.salaryTaxRevenue,";
        $dql .= "t.pensionNbTaxHomes, t.pensionTaxRevenue";

        $query = $qb->select($dql)
            ->from($this->_entityName, 't')
            ->where('t.areaLabel = :areaLabel')
            ->setParameter('areaLabel', $areaLabel)
            ->orderBy('t.year', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

    // Tax area
    public function getAreaTaxByArea($areaLabel) {
        $qb = $this->_em->createQueryBuilder();

        $dql = "DISTINCT t.year, t.codeInsee, t.communeLabel, t.departmentLabel, t.nbTaxHomes, t.taxRevenue, t.totalAmount, ";
        $dql .= "t.nbImposableTaxHomes, t.imposableTaxRevenue, t.salaryNbTaxHomes, t.salaryTaxRevenue,";
        $dql .= "t.pensionNbTaxHomes, t.pensionTaxRevenue";

        $query = $qb->select($dql)
            ->from($this->_entityName, 't')
            ->where('t.areaLabel = :areaLabel')
            ->setParameter('areaLabel', $areaLabel)
            ->orderBy('t.year', 'DESC')
            ->getQuery();

        return $query->getResult();
    }
}
