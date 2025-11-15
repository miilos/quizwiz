<?php

namespace App\EventListener;

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
            $exception instanceof UnprocessableEntityHttpException => $this->handleValidationErrorException($exception),
            default => new JsonResponse([
                'status' => 'error',
                'message' => 'Something went wrong!',
            ], 500)
        };

        $event->setResponse($error);
    }

    private function getResponse(\Throwable $t): JsonResponse
    {
        return new JsonResponse([
            'status' => 'error',
            'message' => $t->getMessage(),
        ], $t->getStatusCode());
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
}
