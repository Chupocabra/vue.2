<?php

namespace App\Entity;

use App\Repository\EducationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EducationRepository::class)]
class Education
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $University = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Faculty = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Specialization = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $EndYear = null;

    #[ORM\ManyToOne(inversedBy: 'education')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Resume $resume = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(string $Type): self
    {
        $this->Type = $Type;

        return $this;
    }

    public function getUniversity(): ?string
    {
        return $this->University;
    }

    public function setUniversity(?string $University): self
    {
        $this->University = $University;

        return $this;
    }

    public function getFaculty(): ?string
    {
        return $this->Faculty;
    }

    public function setFaculty(?string $Faculty): self
    {
        $this->Faculty = $Faculty;

        return $this;
    }

    public function getSpecialization(): ?string
    {
        return $this->Specialization;
    }

    public function setSpecialization(?string $Specialization): self
    {
        $this->Specialization = $Specialization;

        return $this;
    }

    public function getEndYear(): ?string
    {
        return $this->EndYear;
    }

    public function setEndYear(?string $EndYear): self
    {
        $this->EndYear = $EndYear;

        return $this;
    }

    public function getResume(): ?Resume
    {
        return $this->resume;
    }

    public function setResume(?Resume $resume): self
    {
        $this->resume = $resume;

        return $this;
    }
}
