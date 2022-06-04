<?php

namespace App\Repository;

use App\Entity\Accounting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Accounting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Accounting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Accounting[]    findAll()
 * @method Accounting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Accounting::class);
    }

    /**
     * @return \Doctrine\DBAL\Cache\ArrayStatement|\Doctrine\DBAL\Cache\ResultCacheStatement|\Doctrine\DBAL\Driver\ResultStatement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function resetTable() {
        $qb = $this->_em->createQueryBuilder();

        // Delete all data
        $qb->delete()
            ->from($this->_entityName, 'a')
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
            $this->createQueryBuilder('a')
                ->select('a.source')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
    }


    // Accounting commune
    public function getCommuneAccountingByCommune($codeInsee) {
        $qb = $this->_em->createQueryBuilder();

        $dql = "DISTINCT a.year, a.codeInsee, a.groupingType, a.population,";
        $dql .= "a.productsTotal, a.localTax, a.otherTax,";
        $dql .= "a.globalAllocation, a.totalExpenses, a.personalExpenses,";
        $dql .= "a.externalExpenses, a.financialExpenses, a.grants,";
        $dql .= "a.housingTax, a.propertyTax, a.noPropertyTax,";
        $dql .= "a.brankCredits, a.receivedGrants, a.equipmentExpenses,";
        $dql .= "a.creditRefund, a.debtAnnuity";

        $query = $qb->select($dql)
            ->from($this->_entityName, 'a')
            ->where('a.codeInsee = :codeInsee')
            ->setParameter('codeInsee', $codeInsee)
            ->orderBy('a.year', 'DESC')
            ->getQuery();

        return $query->getResult();
    }
    public function getCommuneAccountingByDepartment($departmentLabel) {

        $qb = $this->_em->createQueryBuilder();

        $dql = "DISTINCT a.year, a.codeInsee, a.communeLabel, a.groupingType, a.population,";
        $dql .= "a.productsTotal, a.localTax, a.otherTax,";
        $dql .= "a.globalAllocation, a.totalExpenses, a.personalExpenses,";
        $dql .= "a.externalExpenses, a.financialExpenses, a.grants,";
        $dql .= "a.housingTax, a.propertyTax, a.noPropertyTax,";
        $dql .= "a.brankCredits, a.receivedGrants, a.equipmentExpenses,";
        $dql .= "a.creditRefund, a.debtAnnuity";

        $query = $qb->select($dql)
            ->from($this->_entityName, 'a')
            ->where('a.departmentLabel = :departmentLabel')
            ->setParameter('departmentLabel', $departmentLabel)
            ->orderBy('a.year', 'DESC')
            ->getQuery();

        return $query->getResult();

    }
    public function getCommuneAccountingByArea($areaLabel) {

        $qb = $this->_em->createQueryBuilder();

        $dql = "DISTINCT a.year, a.codeInsee, a.communeLabel, a.departmentLabel, a.groupingType, a.population,";
        $dql .= "a.productsTotal, a.localTax, a.otherTax,";
        $dql .= "a.globalAllocation, a.totalExpenses, a.personalExpenses,";
        $dql .= "a.externalExpenses, a.financialExpenses, a.grants,";
        $dql .= "a.housingTax, a.propertyTax, a.noPropertyTax,";
        $dql .= "a.brankCredits, a.receivedGrants, a.equipmentExpenses,";
        $dql .= "a.creditRefund, a.debtAnnuity";

        $query = $qb->select($dql)
            ->from($this->_entityName, 'a')
            ->where('a.areaLabel = :areaLabel')
            ->setParameter('areaLabel', $areaLabel)
            ->orderBy('a.year', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

    public function getDepartmentAccountingByDepartment($departmentLabel) {

        $qb = $this->_em->createQueryBuilder();

        $dql = "DISTINCT a.year, a.codeInsee, a.communeLabel, a.groupingType, a.population,";
        $dql .= "a.productsTotal, a.localTax, a.otherTax,";
        $dql .= "a.globalAllocation, a.totalExpenses, a.personalExpenses,";
        $dql .= "a.externalExpenses, a.financialExpenses, a.grants,";
        $dql .= "a.housingTax, a.propertyTax, a.noPropertyTax,";
        $dql .= "a.brankCredits, a.receivedGrants, a.equipmentExpenses,";
        $dql .= "a.creditRefund, a.debtAnnuity";

        $query = $qb->select($dql)
            ->from($this->_entityName, 'a')
            ->where('a.departmentLabel = :departmentLabel')
            ->setParameter('departmentLabel', $departmentLabel)
            ->orderBy('a.year', 'DESC')
            ->getQuery();

        return $query->getResult();
    }
    public function getDepartmentAccountingByArea($areaLabel) {
        $qb = $this->_em->createQueryBuilder();

        $dql = "DISTINCT a.year, a.codeInsee, a.communeLabel, a.departmentLabel, a.groupingType, a.population,";
        $dql .= "a.productsTotal, a.localTax, a.otherTax,";
        $dql .= "a.globalAllocation, a.totalExpenses, a.personalExpenses,";
        $dql .= "a.externalExpenses, a.financialExpenses, a.grants,";
        $dql .= "a.housingTax, a.propertyTax, a.noPropertyTax,";
        $dql .= "a.brankCredits, a.receivedGrants, a.equipmentExpenses,";
        $dql .= "a.creditRefund, a.debtAnnuity";


        $query = $qb->select($dql)
            ->from($this->_entityName, 'a')
            ->where('a.areaLabel = :areaLabel')
            ->setParameter('areaLabel', $areaLabel)
            ->orderBy('a.year', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

    // Accounting Area
    public function getAreaAccountingByArea($areaLabel) {
        $qb = $this->_em->createQueryBuilder();

        $dql = "DISTINCT a.year, a.codeInsee, a.communeLabel, a.departmentLabel, a.groupingType, a.population,";
        $dql .= "a.productsTotal, a.localTax, a.otherTax,";
        $dql .= "a.globalAllocation, a.totalExpenses, a.personalExpenses,";
        $dql .= "a.externalExpenses, a.financialExpenses, a.grants,";
        $dql .= "a.housingTax, a.propertyTax, a.noPropertyTax,";
        $dql .= "a.brankCredits, a.receivedGrants, a.equipmentExpenses,";
        $dql .= "a.creditRefund, a.debtAnnuity";

        $query = $qb->select($dql)
            ->from($this->_entityName, 'a')
            ->where('a.areaLabel = :areaLabel')
            ->setParameter('areaLabel', $areaLabel)
            ->orderBy('a.year', 'DESC')
            ->getQuery();

        return $query->getResult();
    }
}
