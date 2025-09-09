<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    protected string $name;

    #[ORM\Column(length: 255)]
    protected string $work;

    #[ORM\Column(type: 'datetime')]
    protected DateTime $birthDate;

    #[ORM\Column(type: 'boolean')]
    protected bool $acceptsCommercial = false;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(?int $id): void
    {
        $this->id = $id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    public function getWork(): string
    {
        return $this->work;
    }
    public function setWork(string $work): void
    {
        $this->work = $work;
    }
    public function getBirthDate(): DateTime
    {
        return $this->birthDate;
    }
    public function setBirthDate(DateTime $birthDate): void
    {
        $this->birthDate = $birthDate;
    }
    public function getAcceptsCommercial(): bool
    {
        return $this->acceptsCommercial;
    }
    public function setAcceptsCommercial(bool $acceptsCommercial): void
    {
        $this->acceptsCommercial = $acceptsCommercial;
    }

}
