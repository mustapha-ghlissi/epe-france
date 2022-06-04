<?php

namespace App\Entity;

use App\Repository\RegionalAdvisorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=RegionalAdvisorRepository::class)
 */
class RegionalAdvisor
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"search:read", "index:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $regionCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $regionLabel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $departmentCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"search:read"})
     */
    private $departmentLabel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $departmentCapital;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $departmentPopulation;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $departmentSurface;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $departmentDensity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $areaLabel;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbDepartments;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $areaCapital;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $areaSurface;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $areaPopulation;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $areaDensity;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"search:read", "note:read", "index:read"})
     * @Assert\NotBlank(message="Champ requis")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"search:read", "note:read", "index:read"})
     * @Assert\NotBlank(message="Champ requis")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=5)
     * @Groups({"index:read"})
     * @Assert\NotBlank(message="Champ requis")
     */
    private $gender;

    /**
     * @ORM\Column(type="date")
     * @Groups({"index:read"})
     * @Assert\NotBlank(message="Champ requis")
     */
    private $birthDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $professionCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $professionLabel;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $mandateStartDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $functionLabel;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $functionStartDate;

    /**
     * @ORM\OneToOne(targetEntity=ExtraData::class, mappedBy="regionalAdvisor", cascade={"persist", "remove"})
     */
    private $extraData;

    /**
     * @ORM\OneToMany(targetEntity=MPDPRNote::class, mappedBy="regionalPresident", orphanRemoval=true)
     */
    private $notes;

    /**
     * @ORM\OneToMany(targetEntity=OtherNote::class, mappedBy="regionalAdvisor", orphanRemoval=true)
     */
    private $otherNotes;

    /**
     * @ORM\OneToMany(targetEntity=BoardMinute::class, mappedBy="regionalAdvisor", orphanRemoval=true)
     */
    private $boardMinutes;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     */
    private $latitude;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=8, nullable=true)
     */
    private $longitude;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbPublicService;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $populationYear;


    public function __construct()
    {
        $this->notes = new ArrayCollection();
        $this->otherNotes = new ArrayCollection();
        $this->boardMinutes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRegionCode(): ?string
    {
        return $this->regionCode;
    }

    public function setRegionCode(?string $regionCode): self
    {
        $this->regionCode = $regionCode;

        return $this;
    }

    public function getRegionLabel(): ?string
    {
        return $this->regionLabel;
    }

    public function setRegionLabel(?string $regionLabel): self
    {
        $this->regionLabel = $regionLabel;

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

    public function getDepartmentCapital(): ?string
    {
        return $this->departmentCapital;
    }

    public function setDepartmentCapital(?string $departmentCapital): self
    {
        $this->departmentCapital = $departmentCapital;

        return $this;
    }

    public function getDepartmentPopulation(): ?int
    {
        return $this->departmentPopulation;
    }

    public function setDepartmentPopulation(?int $departmentPopulation): self
    {
        $this->departmentPopulation = $departmentPopulation;

        return $this;
    }

    public function getDepartmentSurface(): ?int
    {
        return $this->departmentSurface;
    }

    public function setDepartmentSurface(?int $departmentSurface): self
    {
        $this->departmentSurface = $departmentSurface;

        return $this;
    }

    public function getDepartmentDensity(): ?int
    {
        return $this->departmentDensity;
    }

    public function setDepartmentDensity(?int $departmentDensity): self
    {
        $this->departmentDensity = $departmentDensity;

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

    public function getNbDepartments(): ?int
    {
        return $this->nbDepartments;
    }

    public function setNbDepartments(?int $nbDepartments): self
    {
        $this->nbDepartments = $nbDepartments;

        return $this;
    }

    public function getAreaCapital(): ?string
    {
        return $this->areaCapital;
    }

    public function setAreaCapital(?string $areaCapital): self
    {
        $this->areaCapital = $areaCapital;

        return $this;
    }

    public function getAreaSurface(): ?int
    {
        return $this->areaSurface;
    }

    public function setAreaSurface(?int $areaSurface): self
    {
        $this->areaSurface = $areaSurface;

        return $this;
    }

    public function getAreaPopulation(): ?int
    {
        return $this->areaPopulation;
    }

    public function setAreaPopulation(?int $areaPopulation): self
    {
        $this->areaPopulation = $areaPopulation;

        return $this;
    }

    public function getAreaDensity(): ?int
    {
        return $this->areaDensity;
    }

    public function setAreaDensity(?int $areaDensity): self
    {
        $this->areaDensity = $areaDensity;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getProfessionCode(): ?string
    {
        return $this->professionCode;
    }

    public function setProfessionCode(?string $professionCode): self
    {
        $this->professionCode = $professionCode;

        return $this;
    }

    public function getProfessionLabel(): ?string
    {
        return $this->professionLabel;
    }

    public function setProfessionLabel(?string $professionLabel): self
    {
        $this->professionLabel = $professionLabel;

        return $this;
    }

    public function getMandateStartDate(): ?\DateTimeInterface
    {
        return $this->mandateStartDate;
    }

    public function setMandateStartDate(?\DateTimeInterface $mandateStartDate): self
    {
        $this->mandateStartDate = $mandateStartDate;

        return $this;
    }

    public function getFunctionLabel(): ?string
    {
        return $this->functionLabel;
    }

    public function setFunctionLabel(?string $functionLabel): self
    {
        $this->functionLabel = $functionLabel;

        return $this;
    }

    public function getFunctionStartDate(): ?\DateTimeInterface
    {
        return $this->functionStartDate;
    }

    public function setFunctionStartDate(?\DateTimeInterface $functionStartDate): self
    {
        $this->functionStartDate = $functionStartDate;

        return $this;
    }

    public function getExtraData(): ?ExtraData
    {
        return $this->extraData;
    }

    public function setExtraData(?ExtraData $extraData): self
    {
        $this->extraData = $extraData;

        // set (or unset) the owning side of the relation if necessary
        $newRegionalAdvisor = null === $extraData ? null : $this;
        if ($extraData->getRegionalAdvisor() !== $newRegionalAdvisor) {
            $extraData->setRegionalAdvisor($newRegionalAdvisor);
        }

        return $this;
    }

    /**
     * @return Collection|MPDPRNote[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(MPDPRNote $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setRegionalPresident($this);
        }

        return $this;
    }

    public function removeNote(MPDPRNote $note): self
    {
        if ($this->notes->contains($note)) {
            $this->notes->removeElement($note);
            // set the owning side to null (unless already changed)
            if ($note->getRegionalPresident() === $this) {
                $note->setRegionalPresident(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|OtherNote[]
     */
    public function getOtherNotes(): Collection
    {
        return $this->otherNotes;
    }

    public function addOtherNote(OtherNote $otherNote): self
    {
        if (!$this->otherNotes->contains($otherNote)) {
            $this->otherNotes[] = $otherNote;
            $otherNote->setRegionalAdvisor($this);
        }

        return $this;
    }

    public function removeOtherNote(OtherNote $otherNote): self
    {
        if ($this->otherNotes->contains($otherNote)) {
            $this->otherNotes->removeElement($otherNote);
            // set the owning side to null (unless already changed)
            if ($otherNote->getRegionalAdvisor() === $this) {
                $otherNote->setRegionalAdvisor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BoardMinute[]
     */
    public function getBoardMinutes(): Collection
    {
        return $this->boardMinutes;
    }

    public function addBoardMinute(BoardMinute $boardMinute): self
    {
        if (!$this->boardMinutes->contains($boardMinute)) {
            $this->boardMinutes[] = $boardMinute;
            $boardMinute->setRegionalAdvisor($this);
        }

        return $this;
    }

    public function removeBoardMinute(BoardMinute $boardMinute): self
    {
        if ($this->boardMinutes->contains($boardMinute)) {
            $this->boardMinutes->removeElement($boardMinute);
            // set the owning side to null (unless already changed)
            if ($boardMinute->getRegionalAdvisor() === $this) {
                $boardMinute->setRegionalAdvisor(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getNbPublicService(): ?int
    {
        return $this->nbPublicService;
    }

    public function setNbPublicService(?int $nbPublicService): self
    {
        $this->nbPublicService = $nbPublicService;

        return $this;
    }

    public function getPopulationYear(): ?int
    {
        return $this->populationYear;
    }

    public function setPopulationYear(?int $populationYear): self
    {
        $this->populationYear = $populationYear;

        return $this;
    }


}
