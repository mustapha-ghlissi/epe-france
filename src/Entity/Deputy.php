<?php

namespace App\Entity;

use App\Repository\DeputyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DeputyRepository::class)
 */
class Deputy
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
     * @ORM\Column(type="float", nullable=true)
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
     * @ORM\Column(type="float", nullable=true)
     */
    private $areaDensity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $legislativeCirCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $legislativeCirLabel;

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
     * @ORM\OneToOne(targetEntity=ExtraData::class, mappedBy="deputy", cascade={"persist", "remove"})
     */
    private $extraData;

    /**
     * @ORM\OneToMany(targetEntity=DeputyNote::class, mappedBy="deputy", orphanRemoval=true)
     */
    private $notes;

    /**
     * @ORM\OneToMany(targetEntity=BoardMinute::class, mappedBy="deputy", orphanRemoval=true)
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
        $this->boardMinutes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDepartmentDensity(): ?float
    {
        return $this->departmentDensity;
    }

    public function setDepartmentDensity(?float $departmentDensity): self
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

    public function getAreaDensity(): ?float
    {
        return $this->areaDensity;
    }

    public function setAreaDensity(?float $areaDensity): self
    {
        $this->areaDensity = $areaDensity;

        return $this;
    }

    public function getLegislativeCirCode(): ?string
    {
        return $this->legislativeCirCode;
    }

    public function setLegislativeCirCode(?string $legislativeCirCode): self
    {
        $this->legislativeCirCode = $legislativeCirCode;

        return $this;
    }

    public function getLegislativeCirLabel(): ?string
    {
        return $this->legislativeCirLabel;
    }

    public function setLegislativeCirLabel(?string $legislativeCirLabel): self
    {
        $this->legislativeCirLabel = $legislativeCirLabel;

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

    public function getExtraData(): ?ExtraData
    {
        return $this->extraData;
    }

    public function setExtraData(?ExtraData $extraData): self
    {
        $this->extraData = $extraData;

        // set (or unset) the owning side of the relation if necessary
        $newDeputy = null === $extraData ? null : $this;
        if ($extraData->getDeputy() !== $newDeputy) {
            $extraData->setDeputy($newDeputy);
        }

        return $this;
    }

    /**
     * @return Collection|DeputyNote[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(DeputyNote $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setDeputy($this);
        }

        return $this;
    }

    public function removeNote(DeputyNote $note): self
    {
        if ($this->notes->contains($note)) {
            $this->notes->removeElement($note);
            // set the owning side to null (unless already changed)
            if ($note->getDeputy() === $this) {
                $note->setDeputy(null);
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
            $boardMinute->setDeputy($this);
        }

        return $this;
    }

    public function removeBoardMinute(BoardMinute $boardMinute): self
    {
        if ($this->boardMinutes->contains($boardMinute)) {
            $this->boardMinutes->removeElement($boardMinute);
            // set the owning side to null (unless already changed)
            if ($boardMinute->getDeputy() === $this) {
                $boardMinute->setDeputy(null);
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
