<?php

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class TagDto
{
    public function __construct(
        #[Groups(['full_quiz_data'])]
        private ?int $id = null,

        #[Groups(['full_quiz_data'])]
        #[Assert\NotNull(message: 'A tag must have a name.')]
        private ?string $name = null,

        #[Groups(['full_quiz_data'])]
        private ?string $displayName = null,
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }
}
