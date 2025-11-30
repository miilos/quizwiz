<?php

namespace App\Service\Converter;

use App\Dto\EntityDtoInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

trait ValidatesDtoTrait
{
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
