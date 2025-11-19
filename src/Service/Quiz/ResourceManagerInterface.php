<?php

namespace App\Service\Quiz;

use App\Dto\ResourceInterface;

interface ResourceManagerInterface
{
    public function create(array $data): ResourceInterface;
}
