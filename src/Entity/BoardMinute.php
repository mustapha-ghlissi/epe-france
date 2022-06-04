<?php

namespace App\Entity;

use App\Repository\BoardMinuteRepository;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BoardMinuteRepository::class)
 */
class BoardMinute
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champs est requis")
     */
    private $title;

    /**
     * @ORM\Column(type="array")
     */
    private $fileNames;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Ce champs est requis")
     */
    private $year;

    /**
     * @ORM\Column(type="string", length=16)
     * @Assert\NotBlank(message="Ce champs est requis")
     */
    private $month;

    /**
     * @ORM\ManyToOne(targetEntity=Commune::class)
     */
    private $commune;

    /**
     * @ORM\ManyToOne(targetEntity=Department::class)
     */
    private $department;

    /**
     * @ORM\ManyToOne(targetEntity=Area::class)
     */
    private $area;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getFileNames(): ?array
    {
        return $this->fileNames;
    }

    public function setFileNames(array $fileNames): self
    {
        $this->fileNames = $fileNames;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getMonth(): ?string
    {
        return $this->month;
    }

    public function setMonth(string $month): self
    {
        $this->month = $month;

        return $this;
    }

    public function getCommune(): ?Commune
    {
        return $this->commune;
    }

    public function setCommune(?Commune $commune): self
    {
        $this->commune = $commune;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

        return $this;
    }

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function setArea(?Area $area): self
    {
        $this->area = $area;

        return $this;
    }
}
