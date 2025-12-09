<?php

namespace App\Service\Converter;

use App\Dto\EntityDtoInterface;
use App\Entity\EntityInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class EntityConverter
{
    abstract public function toDto(array $data): EntityDtoInterface;
    abstract public function entityToDto(EntityInterface $entity): EntityDtoInterface;
}
