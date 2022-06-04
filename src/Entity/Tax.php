<?php

namespace App\Entity;

use App\Repository\TaxRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TaxRepository::class)
 */
class Tax
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank(message="Champ requis")
     */
    private $year;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Champ requis")
     */
    private $codeInsee;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Champ requis")
     */
    private $communeLabel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Champ requis")
     */
    private $communeLastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Champ requis")
     */
    private $communeFirstName;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\NotBlank(message="Champ requis")
     */
    private $communeBirthDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Champ requis")
     */
    private $departmentCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Champ requis")
     */
    private $departmentLabel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Champ requis")
     */
    private $depLastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Champ requis")
     */
    private $depFirstName;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\NotBlank(message="Champ requis")
     */
    private $depBirthDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Champ requis")
     */
    private $areaLabel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Champ requis")
     */
    private $areaLastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Champ requis")
     */
    private $areaFirstName;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\NotBlank(message="Champ requis")
     */
    private $areaBirthDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbTaxHomes;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $taxRevenue;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalAmount;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbImposableTaxHomes;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $imposableTaxRevenue;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $salaryNbTaxHomes;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $salaryTaxRevenue;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $pensionNbTaxHomes;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $pensionTaxRevenue;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $source;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getCodeInsee(): ?string
    {
        return $this->codeInsee;
    }

    public function setCodeInsee(?string $codeInsee): self
    {
        $this->codeInsee = $codeInsee;

        return $this;
    }

    public function getCommuneLabel(): ?string
    {
        return $this->communeLabel;
    }

    public function setCommuneLabel(?string $communeLabel): self
    {
        $this->communeLabel = $communeLabel;

        return $this;
    }

    public function getCommuneLastName(): ?string
    {
        return $this->communeLastName;
    }

    public function setCommuneLastName(?string $communeLastName): self
    {
        $this->communeLastName = $communeLastName;

        return $this;
    }

    public function getCommuneFirstName(): ?string
    {
        return $this->communeFirstName;
    }

    public function setCommuneFirstName(?string $communeFirstName): self
    {
        $this->communeFirstName = $communeFirstName;

        return $this;
    }

    public function getCommuneBirthDate(): ?\DateTimeInterface
    {
        return $this->communeBirthDate;
    }

    public function setCommuneBirthDate(?\DateTimeInterface $communeBirthDate): self
    {
        $this->communeBirthDate = $communeBirthDate;

        return $this;
    }

    public function getDepartmentCode(): ?string
    {
        return $this->departmentCode;
    }

    public function setDepartmentCode(?string $departmentCode): self
    {
        $this->departmentCode = $departmentCode;

        return $this;
    }

    public function getDepartmentLabel(): ?string
    {
        return $this->departmentLabel;
    }

    public function setDepartmentLabel(?string $departmentLabel): self
    {
        $this->departmentLabel = $departmentLabel;

        return $this;
    }

    public function getDepLastName(): ?string
    {
        return $this->depLastName;
    }

    public function setDepLastName(?string $depLastName): self
    {
        $this->depLastName = $depLastName;

        return $this;
    }

    public function getDepFirstName(): ?string
    {
        return $this->depFirstName;
    }

    public function setDepFirstName(?string $depFirstName): self
    {
        $this->depFirstName = $depFirstName;

        return $this;
    }

    public function getDepBirthDate(): ?\DateTimeInterface
    {
        return $this->depBirthDate;
    }

    public function setDepBirthDate(?\DateTimeInterface $depBirthDate): self
    {
        $this->depBirthDate = $depBirthDate;

        return $this;
    }

    public function getAreaLabel(): ?string
    {
        return $this->areaLabel;
    }

    public function setAreaLabel(?string $areaLabel): self
    {
        $this->areaLabel = $areaLabel;

        return $this;
    }

    public function getAreaLastName(): ?string
    {
        return $this->areaLastName;
    }

    public function setAreaLastName(?string $areaLastName): self
    {
        $this->areaLastName = $areaLastName;

        return $this;
    }

    public function getAreaFirstName(): ?string
    {
        return $this->areaFirstName;
    }

    public function setAreaFirstName(?string $areaFirstName): self
    {
        $this->areaFirstName = $areaFirstName;

        return $this;
    }

    public function getAreaBirthDate(): ?\DateTimeInterface
    {
        return $this->areaBirthDate;
    }

    public function setAreaBirthDate(?\DateTimeInterface $areaBirthDate): self
    {
        $this->areaBirthDate = $areaBirthDate;

        return $this;
    }

    public function getNbTaxHomes(): ?int
    {
        return $this->nbTaxHomes;
    }

    public function setNbTaxHomes(?int $nbTaxHomes): self
    {
        $this->nbTaxHomes = $nbTaxHomes;

        return $this;
    }

    public function getTaxRevenue(): ?string
    {
        return $this->taxRevenue;
    }

    public function setTaxRevenue(?string $taxRevenue): self
    {
        $this->taxRevenue = $taxRevenue;

        return $this;
    }

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(?string $totalAmount): self
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getNbImposableTaxHomes(): ?int
    {
        return $this->nbImposableTaxHomes;
    }

    public function setNbImposableTaxHomes(?int $nbImposableTaxHomes): self
    {
        $this->nbImposableTaxHomes = $nbImposableTaxHomes;

        return $this;
    }

    public function getImposableTaxRevenue(): ?string
    {
        return $this->imposableTaxRevenue;
    }

    public function setImposableTaxRevenue(?string $imposableTaxRevenue): self
    {
        $this->imposableTaxRevenue = $imposableTaxRevenue;

        return $this;
    }

    public function getSalaryNbTaxHomes(): ?int
    {
        return $this->salaryNbTaxHomes;
    }

    public function setSalaryNbTaxHomes(?int $salaryNbTaxHomes): self
    {
        $this->salaryNbTaxHomes = $salaryNbTaxHomes;

        return $this;
    }

    public function getSalaryTaxRevenue(): ?string
    {
        return $this->salaryTaxRevenue;
    }

    public function setSalaryTaxRevenue(?string $salaryTaxRevenue): self
    {
        $this->salaryTaxRevenue = $salaryTaxRevenue;

        return $this;
    }

    public function getPensionNbTaxHomes(): ?int
    {
        return $this->pensionNbTaxHomes;
    }

    public function setPensionNbTaxHomes(?int $pensionNbTaxHomes): self
    {
        $this->pensionNbTaxHomes = $pensionNbTaxHomes;

        return $this;
    }

    public function getPensionTaxRevenue(): ?string
    {
        return $this->pensionTaxRevenue;
    }

    public function setPensionTaxRevenue(?string $pensionTaxRevenue): self
    {
        $this->pensionTaxRevenue = $pensionTaxRevenue;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source;

        return $this;
    }
}
