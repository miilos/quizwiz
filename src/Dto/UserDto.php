<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UserDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'You must enter a username.')]
        private ?string $username = null,

        #[Assert\Email(message: 'Not a valid email address.')]
        private ?string $email = null,

        #[Assert\NotBlank(message: 'You must enter a password.')]
        private ?string $password = null,

        private array $roles = ['ROLE_USER'],

        private ?string $accountActivationToken = null,
    ) {}

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getAccountActivationToken(): ?string
    {
        return $this->accountActivationToken;
    }

    public function setAccountActivationToken(?string $accountActivationToken): self
    {
        $this->accountActivationToken = $accountActivationToken;

        return $this;
    }
}
