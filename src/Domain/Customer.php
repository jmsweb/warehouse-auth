<?php

namespace App\Domain;

use DateTime;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Id;

#[Entity]
#[Table(name:'Customer')]
class Customer {
    #[Id, Column(name: 'customer_id', type: 'guid')]
    protected string $id;

    #[Column(name: 'email', type: 'string')]
    public string $email;

    #[Column(name: 'password', type: 'string')]
    protected string $password;

    #[Column(name: 'first_name', type: 'string')]
    public string $firstName;

    #[Column(name: 'last_name', type: 'string')]
    public string $lastName;

    #[Column(name: 'is_admin', type: 'boolean')]
    public bool $isAdmin;

    #[Column(name: 'date_create', type: 'datetime')]
    protected DateTime $createDate;

    #[Column(name: 'date_register', type: 'datetime')]
    protected DateTime $registerDate;

    #[Column(name: 'last_updated', type: 'datetime')]
    public DateTime $lastUpdated;

    public function getId(): string {
        return $this->id;
    }

    public function setId(string $id): void {
        $this->id = $id;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): void {
        $this->password = $password;
    }

    public function getFirstName(): string {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void {
        $this->firstName = $firstName;
    }

    public function getLastName(): string {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void {
        $this->lastName = $lastName;
    }

    public function getIsAdmin(): bool {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): void {
        $this->isAdmin = $isAdmin;
    }

    public function getCreateDate(): DateTime {
        return $this->createDate;
    }

    public function setCreateDate(DateTime $createDate): void {
        $this->createDate = $createDate;
    }

    public function getRegisterDate(): DateTime {
        return $this->registerDate;
    }

    public function setRegisterDate(DateTime $registerDate): void {
        $this->registerDate = $registerDate;
    }

    public function getLastUpdated(): DateTime {
        return $this->lastUpdated;
    }

    public function setLastUpdated(DateTime $lastUpdated): void {
        $this->lastUpdated = $lastUpdated;
    }
}