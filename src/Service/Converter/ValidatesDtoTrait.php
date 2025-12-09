<?php

namespace App\Service\Converter;

use App\Dto\EntityDtoInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait ValidatesDtoTrait
{
    /** @throws UnprocessableEntityHttpException */
    public static function validateDto(EntityDtoInterface $dto, ValidatorInterface $validator): void
    {
        $violations = $validator->validate($dto);
        if (count($violations) > 0) {
            throw new UnprocessableEntityHttpException(
                'Validation failed.',
                previous: new ValidationFailedException($dto, $violations)
            );
        }
    }
}
