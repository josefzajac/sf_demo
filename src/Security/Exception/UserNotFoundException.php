<?php declare(strict_types=1);

namespace Document\Security\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Throwable;

#[WithHttpStatus(403)]
class UserNotFoundException extends \RuntimeException
{
    public function __construct(string $message, int $code = Response::HTTP_FORBIDDEN, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
