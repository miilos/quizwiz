<?php

namespace App\GraphQL;

use Overblog\GraphQLBundle\Error\UserError;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class GraphQLUserErrorService
{
    public static function throwValidationFailedUserError(ConstraintViolationListInterface $violations): void
    {
        $errorMessage = 'Validation failed.\n';

        foreach ($violations as $violation) {
            $errorMessage .= sprintf('%s: %s\n',
                $violation->getPropertyPath(),
                $violation->getMessage()
            );
        }

        throw new UserError($errorMessage);
    }
}
