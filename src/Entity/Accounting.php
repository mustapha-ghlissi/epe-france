<?php

namespace App\Entity;

use App\Repository\AccountingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AccountingRepository::class)
 */
class Accounting
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
    private $population;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $groupingType;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $productsTotal;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $localTax;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $otherTax;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $globalAllocation;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalExpenses;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $personalExpenses;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $externalExpenses;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $financialExpenses;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $grants;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $housingTax;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $propertyTax;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $noPropertyTax;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $brankCredits;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $receivedGrants;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $equipmentExpenses;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $creditRefund;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $debtAnnuity;

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

    public function getPopulation(): ?int
    {
        return $this->population;
    }

    public function setPopulation(?int $population): self
    {
        $this->population = $population;

        return $this;
    }

    public function getGroupingType(): ?string
    {
        return $this->groupingType;
    }

    public function setGroupingType(?string $groupingType): self
    {
        $this->groupingType = $groupingType;

        return $this;
    }

    public function getProductsTotal(): ?float
    {
        return $this->productsTotal;
    }

    public function setProductsTotal(?float $productsTotal): self
    {
        $this->productsTotal = $productsTotal;

        return $this;
    }

    public function getLocalTax(): ?float
    {
        return $this->localTax;
    }

    public function setLocalTax(?float $localTax): self
    {
        $this->localTax = $localTax;

        return $this;
    }

    public function getOtherTax(): ?float
    {
        return $this->otherTax;
    }

    public function setOtherTax(?float $otherTax): self
    {
        $this->otherTax = $otherTax;

        return $this;
    }

    public function getGlobalAllocation(): ?float
    {
        return $this->globalAllocation;
    }

    public function setGlobalAllocation(?float $globalAllocation): self
    {
        $this->globalAllocation = $globalAllocation;

        return $this;
    }

    public function getTotalExpenses(): ?float
    {
        return $this->totalExpenses;
    }

    public function setTotalExpenses(?float $totalExpenses): self
    {
        $this->totalExpenses = $totalExpenses;

        return $this;
    }

    public function getPersonalExpenses(): ?float
    {
        return $this->personalExpenses;
    }

    public function setPersonalExpenses(?float $personalExpenses): self
    {
        $this->personalExpenses = $personalExpenses;

        return $this;
    }

    public function getExternalExpenses(): ?float
    {
        return $this->externalExpenses;
    }

    public function setExternalExpenses(?float $externalExpenses): self
    {
        $this->externalExpenses = $externalExpenses;

        return $this;
    }

    public function getFinancialExpenses(): ?float
    {
        return $this->financialExpenses;
    }

    public function setFinancialExpenses(?float $financialExpenses): self
    {
        $this->financialExpenses = $financialExpenses;

        return $this;
    }

    public function getGrants(): ?float
    {
        return $this->grants;
    }

    public function setGrants(?float $grants): self
    {
        $this->grants = $grants;

        return $this;
    }

    public function getHousingTax(): ?float
    {
        return $this->housingTax;
    }

    public function setHousingTax(?float $housingTax): self
    {
        $this->housingTax = $housingTax;

        return $this;
    }

    public function getPropertyTax(): ?float
    {
        return $this->propertyTax;
    }

    public function setPropertyTax(?float $propertyTax): self
    {
        $this->propertyTax = $propertyTax;

        return $this;
    }

    public function getNoPropertyTax(): ?float
    {
        return $this->noPropertyTax;
    }

    public function setNoPropertyTax(?float $noPropertyTax): self
    {
        $this->noPropertyTax = $noPropertyTax;

        return $this;
    }

    public function getBrankCredits(): ?float
    {
        return $this->brankCredits;
    }

    public function setBrankCredits(?float $brankCredits): self
    {
        $this->brankCredits = $brankCredits;

        return $this;
    }

    public function getReceivedGrants(): ?float
    {
        return $this->receivedGrants;
    }

    public function setReceivedGrants(?float $receivedGrants): self
    {
        $this->receivedGrants = $receivedGrants;

        return $this;
    }

    public function getEquipmentExpenses(): ?float
    {
        return $this->equipmentExpenses;
    }

    public function setEquipmentExpenses(?float $equipmentExpenses): self
    {
        $this->equipmentExpenses = $equipmentExpenses;

        return $this;
    }

    public function getCreditRefund(): ?float
    {
        return $this->creditRefund;
    }

    public function setCreditRefund(?float $creditRefund): self
    {
        $this->creditRefund = $creditRefund;

        return $this;
    }

    public function getDebtAnnuity(): ?float
    {
        return $this->debtAnnuity;
    }

    public function setDebtAnnuity(?float $debtAnnuity): self
    {
        $this->debtAnnuity = $debtAnnuity;

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
