<?php

namespace App\EventListener;

use App\Exception\AuthException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsEventListener]
class ExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $error = match (true) {
            $exception instanceof UnprocessableEntityHttpException =>
                $this->handleValidationErrorException($exception),
            $exception instanceof AuthException =>
                $this->getResponse($exception),
            default => new JsonResponse([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'details' => $exception->getMessage(),
            ], 500)
        };

        $event->setResponse($error);
    }

    private function getResponse(\Throwable $t): JsonResponse
    {
        return new JsonResponse([
            'status' => 'error',
            'message' => $t->getMessage(),
        ], $t->getCode());
    }

    private function handleValidationErrorException(UnprocessableEntityHttpException $exception): JsonResponse
    {
        /** @var ValidationFailedException $parentException */
        $parentException = $exception->getPrevious();

        /** @var ConstraintViolation[] $violations */
        $violations = $parentException->getViolations();

        $violationMessages = [];
        foreach ($violations as $violation) {
            $violationMessages[] = [
                'entity' => $this->getConstraintViolationEntity($violation),
                'property' => $violation->getPropertyPath(),
                'message' => $violation->getMessage()
            ];
        }

        return new JsonResponse([
            'status' => 'error',
            'message' => 'Validation failed. '.$exception->getMessage(),
            'violations' => $violationMessages,
        ]);
    }

    // assumes constraint validations only occur on dto objects
    private function getConstraintViolationEntity(ConstraintViolation $violation): string
    {
        // get the entity classname
        $entity = $violation->getRoot()::class;

        // remove the namespace
        $entity = substr($entity, strrpos($entity, '\\') + 1);

        // remove the *Dto suffix and return the value
        return strtolower(substr($entity, 0, -3));
    }
}
