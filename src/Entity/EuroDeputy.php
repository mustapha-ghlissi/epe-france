<?php

namespace App\Entity;

use App\Repository\EuroDeputyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EuroDeputyRepository::class)
 */
class EuroDeputy
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"search:read", "index:read"})
     */
    private $id;

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
     * @ORM\OneToOne(targetEntity=ExtraData::class, mappedBy="euroDeputy", cascade={"persist", "remove"})
     */
    private $extraData;

    /**
     * @ORM\OneToMany(targetEntity=EuroDeputyNote::class, mappedBy="euroDeputy", orphanRemoval=true)
     */
    private $euroDeputyNotes;

    /**
     * @ORM\OneToMany(targetEntity=BoardMinute::class, mappedBy="euroDeputy", orphanRemoval=true)
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


    public function __construct()
    {
        $this->euroDeputyNotes = new ArrayCollection();
        $this->boardMinutes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|EuroDeputyNote[]
     */
    public function getEuroDeputyNotes(): Collection
    {
        return $this->euroDeputyNotes;
    }

    public function addEuroDeputyNote(EuroDeputyNote $euroDeputyNote): self
    {
        if (!$this->euroDeputyNotes->contains($euroDeputyNote)) {
            $this->euroDeputyNotes[] = $euroDeputyNote;
            $euroDeputyNote->setEuroDeputy($this);
        }

        return $this;
    }

    public function removeEuroDeputyNote(EuroDeputyNote $euroDeputyNote): self
    {
        if ($this->euroDeputyNotes->contains($euroDeputyNote)) {
            $this->euroDeputyNotes->removeElement($euroDeputyNote);
            // set the owning side to null (unless already changed)
            if ($euroDeputyNote->getEuroDeputy() === $this) {
                $euroDeputyNote->setEuroDeputy(null);
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
            $boardMinute->setEuroDeputy($this);
        }

        return $this;
    }

    public function removeBoardMinute(BoardMinute $boardMinute): self
    {
        if ($this->boardMinutes->contains($boardMinute)) {
            $this->boardMinutes->removeElement($boardMinute);
            // set the owning side to null (unless already changed)
            if ($boardMinute->getEuroDeputy() === $this) {
                $boardMinute->setEuroDeputy(null);
            }
        }

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

    public function getProfessionCode(): ?int
    {
        return $this->professionCode;
    }

    public function setProfessionCode(?int $professionCode): self
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


}
