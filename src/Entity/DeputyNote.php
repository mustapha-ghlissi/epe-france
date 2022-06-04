<?php

namespace App\Entity;

use App\Repository\DeputyNoteRepository;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=DeputyNoteRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class DeputyNote
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
    private $votesNumber = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $participationsNumber = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $suggestionsNumber = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $reportsNumber = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $questionsNumber = 0;

    /**
     * @ORM\ManyToOne(targetEntity=Deputy::class, inversedBy="notes")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"note:read"})
     */
    private $deputy;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;


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

    public function setPresenceNumber(float $presenceNumber): self
    {
        $this->presenceNumber = $presenceNumber;

        return $this;
    }

    public function getAmendmentsNumber(): ?float
    {
        return $this->amendmentsNumber;
    }

    public function setAmendmentsNumber(float $amendmentsNumber): self
    {
        $this->amendmentsNumber = $amendmentsNumber;

        return $this;
    }

    public function getVotesNumber(): ?float
    {
        return $this->votesNumber;
    }

    public function setVotesNumber(float $votesNumber): self
    {
        $this->votesNumber = $votesNumber;

        return $this;
    }

    public function getParticipationsNumber(): ?float
    {
        return $this->participationsNumber;
    }

    public function setParticipationsNumber(float $participationsNumber): self
    {
        $this->participationsNumber = $participationsNumber;

        return $this;
    }

    public function getSuggestionsNumber(): ?float
    {
        return $this->suggestionsNumber;
    }

    public function setSuggestionsNumber(float $suggestionsNumber): self
    {
        $this->suggestionsNumber = $suggestionsNumber;

        return $this;
    }

    public function getReportsNumber(): ?float
    {
        return $this->reportsNumber;
    }

    public function setReportsNumber(float $reportsNumber): self
    {
        $this->reportsNumber = $reportsNumber;

        return $this;
    }

    public function getQuestionsNumber(): ?float
    {
        return $this->questionsNumber;
    }

    public function setQuestionsNumber(float $questionsNumber): self
    {
        $this->questionsNumber = $questionsNumber;

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

    public function getDeputy(): ?Deputy
    {
        return $this->deputy;
    }

    public function setDeputy(?Deputy $deputy): self
    {
        $this->deputy = $deputy;

        return $this;
    }


}
