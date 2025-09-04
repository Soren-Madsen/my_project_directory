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
    public function getAge(): int
    {
        return $this->age;
    }
    public function setAge(int $age): void
    {
        $this->age = $age;
    }
    public function getWork(): string
    {
        return $this->work;
    }
    public function setWork(string $work): void
    {
        $this->work = $work;
    }
}