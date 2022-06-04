<?php

namespace App\Entity;

use App\Repository\OtherNoteRepository;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=OtherNoteRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class OtherNote
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"note:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     * @Groups({"note:read"})
     */
    private $ipAddress;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"note:read"})
     */
    private $evaluationDate;

    /**
     * @ORM\Column(type="float")
     */
    private $presenceNumber = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $amendmentsNumber = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $achievementsNumber = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $worksNumber = 0;

    /**
     * @ORM\ManyToOne(targetEntity=Senator::class, inversedBy="notes")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"note:read"})
     */
    private $senator;

    /**
     * @ORM\ManyToOne(targetEntity=MunicipalAdvisor::class, inversedBy="notes")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"note:read"})
     */
    private $municipalAdvisor;

    /**
     * @ORM\ManyToOne(targetEntity=CommunityAdvisor::class, inversedBy="notes")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"note:read"})
     */
    private $communityAdvisor;

    /**
     * @ORM\ManyToOne(targetEntity=CorsicanAdvisor::class, inversedBy="notes")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"note:read"})
     */
    private $corsicanAdvisor;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity=DepartmentalAdvisor::class, inversedBy="otherNotes")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"note:read"})
     */
    private $departmentalAdvisor;

    /**
     * @ORM\ManyToOne(targetEntity=RegionalAdvisor::class, inversedBy="otherNotes")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"note:read"})
     */
    private $regionalAdvisor;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function getEvaluationDate(): ?\DateTimeInterface
    {
        return $this->evaluationDate;
    }

    /**
     * @return $this
     * @ORM\PrePersist()
     */
    public function setEvaluationDate(): self
    {
        $this->evaluationDate = Carbon::now();

        return $this;
    }

    public function getPresenceNumber(): ?float
    {
        return $this->presenceNumber;
    }

    public function setPresenceNumber(?float $presenceNumber): self
    {
        $this->presenceNumber = $presenceNumber;

        return $this;
    }

    public function getAmendmentsNumber(): ?float
    {
        return $this->amendmentsNumber;
    }

    public function setAmendmentsNumber(?float $amendmentsNumber): self
    {
        $this->amendmentsNumber = $amendmentsNumber;

        return $this;
    }

    public function getAchievementsNumber(): ?float
    {
        return $this->achievementsNumber;
    }

    public function setAchievementsNumber(?float $achievementsNumber): self
    {
        $this->achievementsNumber = $achievementsNumber;

        return $this;
    }

    public function getWorksNumber(): ?float
    {
        return $this->worksNumber;
    }

    public function setWorksNumber(?float $worksNumber): self
    {
        $this->worksNumber = $worksNumber;

        return $this;
    }

    public function getSenator(): ?Senator
    {
        return $this->senator;
    }

    public function setSenator(?Senator $senator): self
    {
        $this->senator = $senator;

        return $this;
    }

    public function getMunicipalAdvisor(): ?MunicipalAdvisor
    {
        return $this->municipalAdvisor;
    }

    public function setMunicipalAdvisor(?MunicipalAdvisor $municipalAdvisor): self
    {
        $this->municipalAdvisor = $municipalAdvisor;

        return $this;
    }

    public function getCommunityAdvisor(): ?CommunityAdvisor
    {
        return $this->communityAdvisor;
    }

    public function setCommunityAdvisor(?CommunityAdvisor $communityAdvisor): self
    {
        $this->communityAdvisor = $communityAdvisor;

        return $this;
    }

    public function getCorsicanAdvisor(): ?CorsicanAdvisor
    {
        return $this->corsicanAdvisor;
    }

    public function setCorsicanAdvisor(?CorsicanAdvisor $corsicanAdvisor): self
    {
        $this->corsicanAdvisor = $corsicanAdvisor;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getDepartmentalAdvisor(): ?DepartmentalAdvisor
    {
        return $this->departmentalAdvisor;
    }

    public function setDepartmentalAdvisor(?DepartmentalAdvisor $departmentalAdvisor): self
    {
        $this->departmentalAdvisor = $departmentalAdvisor;

        return $this;
    }

    public function getRegionalAdvisor(): ?RegionalAdvisor
    {
        return $this->regionalAdvisor;
    }

    public function setRegionalAdvisor(?RegionalAdvisor $regionalAdvisor): self
    {
        $this->regionalAdvisor = $regionalAdvisor;

        return $this;
    }
}
