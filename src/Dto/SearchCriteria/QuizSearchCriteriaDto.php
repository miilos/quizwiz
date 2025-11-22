<?php

namespace App\Dto\SearchCriteria;

class QuizSearchCriteriaDto
{
    public function __construct(
        private ?string $title = null
    ) {}

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }
}
