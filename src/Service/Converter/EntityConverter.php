<?php

namespace App\Service\Converter;

use App\Dto\EntityDtoInterface;
use App\Entity\EntityInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class EntityConverter
{
    public function __construct(
        protected ValidatorInterface $validator
    ) {}

    abstract public function toDto(array $data): EntityDtoInterface;

    abstract public function entityToDto(EntityInterface $entity): EntityDtoInterface;

    /** @throws UnprocessableEntityHttpException */
    public function validateDto(EntityDtoInterface $dto): void
    {
        $violations = $this->validator->validate($dto);
        if (count($violations) > 0) {
            throw new UnprocessableEntityHttpException(
                'Validation failed.',
                previous: new ValidationFailedException($dto, $violations)
            );
        }
    }
}
