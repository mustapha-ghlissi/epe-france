<?php

namespace App\Entity;

use App\Repository\CommuneStatRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommuneStatRepository::class)
 */
class CommuneStat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $areaLabel;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $departmentLabel;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbCommunes;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAreaLabel(): ?string
    {
        return $this->areaLabel;
    }

    public function setAreaLabel(string $areaLabel): self
    {
        $this->areaLabel = $areaLabel;

        return $this;
    }

    public function getDepartmentLabel(): ?string
    {
        return $this->departmentLabel;
    }

    public function setDepartmentLabel(string $departmentLabel): self
    {
        $this->departmentLabel = $departmentLabel;

        return $this;
    }

    public function getNbCommunes(): ?int
    {
        return $this->nbCommunes;
    }

    public function setNbCommunes(int $nbCommunes): self
    {
        $this->nbCommunes = $nbCommunes;

        return $this;
    }
}
