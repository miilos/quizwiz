<?php

namespace App\Validator\EmailNotTaken;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EmailNotTakenValidator extends ConstraintValidator
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof EmailNotTaken) {
            throw new UnexpectedTypeException($constraint, EmailNotTaken::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $user = $this->userRepository->findOneBy(['email' => $value]);

        if (!$user) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ string }}', $value)
            ->addViolation();
    }
}
