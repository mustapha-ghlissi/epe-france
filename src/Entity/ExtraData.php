<?php

namespace App\Entity;

use App\Repository\ExtraDataRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ExtraDataRepository::class)
 */
class ExtraData
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="array")
     */
    private $videos = [];

    /**
     * @ORM\Column(type="array")
     */
    private $socialTimelines = [];

    /**
     * @ORM\OneToOne(targetEntity=Mayor::class, inversedBy="extraData")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $mayor;

    /**
     * @ORM\OneToOne(targetEntity=CommunityAdvisor::class, inversedBy="extraData")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $communityAdvisor;

    /**
     * @ORM\OneToOne(targetEntity=CorsicanAdvisor::class, inversedBy="extraData")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $corsicanAdvisor;

    /**
     * @ORM\OneToOne(targetEntity=DepartmentalAdvisor::class, inversedBy="extraData")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $departmentalAdvisor;

    /**
     * @ORM\OneToOne(targetEntity=Deputy::class, inversedBy="extraData")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $deputy;

    /**
     * @ORM\OneToOne(targetEntity=EuroDeputy::class, inversedBy="extraData")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $euroDeputy;

    /**
     * @ORM\OneToOne(targetEntity=MunicipalAdvisor::class, inversedBy="extraData")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $municipalAdvisor;

    /**
     * @ORM\OneToOne(targetEntity=RegionalAdvisor::class, inversedBy="extraData")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $regionalAdvisor;

    /**
     * @ORM\OneToOne(targetEntity=Senator::class, inversedBy="extraData")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $senator;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVideos(): ?array
    {
        return $this->videos;
    }

    public function setVideos(array $videos): self
    {
        $this->videos = $videos;

        return $this;
    }

    public function getSocialTimelines(): ?array
    {
        return $this->socialTimelines;
    }

    public function setSocialTimelines(array $socialTimelines): self
    {
        $this->socialTimelines = $socialTimelines;

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

    public function getDepartmentalAdvisor(): ?DepartmentalAdvisor
    {
        return $this->departmentalAdvisor;
    }

    public function setDepartmentalAdvisor(?DepartmentalAdvisor $departmentalAdvisor): self
    {
        $this->departmentalAdvisor = $departmentalAdvisor;

        return $this;
    }

    public function getDeputy(): ?Deputy
    {
        return $this->deputy;
    }

    public function setDeputy(?Deputy $deputy): self
    {
        $this->deputy = $deputy;

        return $this;
    }

    public function getEuroDeputy(): ?EuroDeputy
    {
        return $this->euroDeputy;
    }

    public function setEuroDeputy(?EuroDeputy $euroDeputy): self
    {
        $this->euroDeputy = $euroDeputy;

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

    public function getRegionalAdvisor(): ?RegionalAdvisor
    {
        return $this->regionalAdvisor;
    }

    public function setRegionalAdvisor(?RegionalAdvisor $regionalAdvisor): self
    {
        $this->regionalAdvisor = $regionalAdvisor;

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
}
