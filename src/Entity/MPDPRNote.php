<?php

namespace App\Entity;

use App\Repository\MPDPRNoteRepository;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=MPDPRNoteRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class MPDPRNote
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
    private $security = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $socialAction = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $jobProfessionalInsert = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $teaching = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $youthChildhood = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $sports = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $economicalIntervention = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $cityPolitics = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $ruralDevelopment = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $accommodation = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $environment = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $garbage = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $telecoms = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $energy = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $transports = 0;

    /**
     * @ORM\ManyToOne(targetEntity=Mayor::class, inversedBy="notes")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"note:read"})
     */
    private $mayor;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity=DepartmentalAdvisor::class, inversedBy="notes")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"note:read"})
     */
    private $departmentalPresident;

    /**
     * @ORM\ManyToOne(targetEntity=RegionalAdvisor::class, inversedBy="notes")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Groups({"note:read"})
     */
    private $regionalPresident;

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

    public function getSecurity(): ?float
    {
        return $this->security;
    }

    public function setSecurity(float $security): self
    {
        $this->security = $security;

        return $this;
    }

    public function getSocialAction(): ?float
    {
        return $this->socialAction;
    }

    public function setSocialAction(float $socialAction): self
    {
        $this->socialAction = $socialAction;

        return $this;
    }

    public function getJobProfessionalInsert(): ?float
    {
        return $this->jobProfessionalInsert;
    }

    public function setJobProfessionalInsert(float $jobProfessionalInsert): self
    {
        $this->jobProfessionalInsert = $jobProfessionalInsert;

        return $this;
    }

    public function getTeaching(): ?float
    {
        return $this->teaching;
    }

    public function setTeaching(float $teaching): self
    {
        $this->teaching = $teaching;

        return $this;
    }

    public function getYouthChildhood(): ?float
    {
        return $this->youthChildhood;
    }

    public function setYouthChildhood(float $youthChildhood): self
    {
        $this->youthChildhood = $youthChildhood;

        return $this;
    }

    public function getSports(): ?float
    {
        return $this->sports;
    }

    public function setSports(float $sports): self
    {
        $this->sports = $sports;

        return $this;
    }

    public function getEconomicalIntervention(): ?float
    {
        return $this->economicalIntervention;
    }

    public function setEconomicalIntervention(float $economicalIntervention): self
    {
        $this->economicalIntervention = $economicalIntervention;

        return $this;
    }

    public function getCityPolitics(): ?float
    {
        return $this->cityPolitics;
    }

    public function setCityPolitics(float $cityPolitics): self
    {
        $this->cityPolitics = $cityPolitics;

        return $this;
    }

    public function getRuralDevelopment(): ?float
    {
        return $this->ruralDevelopment;
    }

    public function setRuralDevelopment(float $ruralDevelopment): self
    {
        $this->ruralDevelopment = $ruralDevelopment;

        return $this;
    }

    public function getAccommodation(): ?float
    {
        return $this->accommodation;
    }

    public function setAccommodation(float $accommodation): self
    {
        $this->accommodation = $accommodation;

        return $this;
    }

    public function getEnvironment(): ?float
    {
        return $this->environment;
    }

    public function setEnvironment(float $environment): self
    {
        $this->environment = $environment;

        return $this;
    }

    public function getGarbage(): ?float
    {
        return $this->garbage;
    }

    public function setGarbage(float $garbage): self
    {
        $this->garbage = $garbage;

        return $this;
    }

    public function getTelecoms(): ?float
    {
        return $this->telecoms;
    }

    public function setTelecoms(float $telecoms): self
    {
        $this->telecoms = $telecoms;

        return $this;
    }

    public function getEnergy(): ?float
    {
        return $this->energy;
    }

    public function setEnergy(float $energy): self
    {
        $this->energy = $energy;

        return $this;
    }

    public function getTransports(): ?float
    {
        return $this->transports;
    }

    public function setTransports(float $transports): self
    {
        $this->transports = $transports;

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

    public function getMayor(): ?Mayor
    {
        return $this->mayor;
    }

    public function setMayor(?Mayor $mayor): self
    {
        $this->mayor = $mayor;

        return $this;
    }

    public function getDepartmentalPresident(): ?DepartmentalAdvisor
    {
        return $this->departmentalPresident;
    }

    public function setDepartmentalPresident(?DepartmentalAdvisor $departmentalPresident): self
    {
        $this->departmentalPresident = $departmentalPresident;

        return $this;
    }

    public function getRegionalPresident(): ?RegionalAdvisor
    {
        return $this->regionalPresident;
    }

    public function setRegionalPresident(?RegionalAdvisor $regionalPresident): self
    {
        $this->regionalPresident = $regionalPresident;

        return $this;
    }


}
