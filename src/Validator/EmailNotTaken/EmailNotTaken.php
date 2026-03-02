<?php

namespace App\Validator\EmailNotTaken;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class EmailNotTaken extends Constraint
{
    public string $message = 'This email address is already taken.';

    public function __construct(
        mixed $options = null,
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct($options, $groups, $payload);
    }
}
