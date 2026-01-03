<?php

namespace App\Entity\Trait;

use App\GraphQL\GraphQLUserErrorService;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait EntityValidatorTrait
{
    private static function validate(object $entity, ValidatorInterface $validator): void
    {
        $violations = $validator->validate($entity);

        if (count($violations) > 0) {
            GraphQLUserErrorService::throwValidationFailedUserError($violations);
        }
    }
}
