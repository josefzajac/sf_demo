<?php declare(strict_types=1);

namespace Document\Modules\General\Exception;

use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

abstract class AppException extends RuntimeException implements HttpExceptionInterface
{
    abstract public function getStatusCode(): int;

    /**
     * @return array<string>
     */
    abstract public function getHeaders(): array;
}
