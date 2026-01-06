<?php

namespace App\Dto\SearchCriteria;

class QuizSearchCriteriaDto
{
    public function __construct(
        private ?string $keywords = null
    ) {}

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }
}
