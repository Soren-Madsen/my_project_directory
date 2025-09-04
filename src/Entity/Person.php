<?php

namespace App\Entity;

class Person
{
    protected string $name;
    protected int $age;
    protected string $work;

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
    protected \DateTime $birthDate;

    public function getBirthDate(): \DateTime
    {
        return $this->birthDate;
    }
    public function setBirthDate(\DateTime $birthDate): void
    {
        $this->birthDate = $birthDate;
    }
}
