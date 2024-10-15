<?php declare(strict_types=1);

namespace Document\Controller;

use Document\Modules\File\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

/**
 * @property SerializerInterface $serializer
 * @property ValidatorInterface $validator
 */
trait ControllerTrait
{
    private function validateAndCall(string $dtoClass, callable $callback, Request $request): mixed
    {
        if ($request->getContent() === '') {
            throw new ValidationException('Missing request body');
        }

        try {
            $dto = $this->serializer->deserialize($request->getContent(), $dtoClass, 'json');
            $errors = $this->validator->validate($dto);
        } catch (Throwable $e) {
            throw new ValidationException($e->getMessage());
        }

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                throw new ValidationException($error->getPropertyPath() . ': ' . $error->getMessage());
            }
        }

        return $callback($dto);
    }
}
