<?php

namespace App\Service\Quiz;

use App\Dto\EntityDtoInterface;

interface ResourceManagerInterface
{
    public function create(array $data): EntityDtoInterface;
}
