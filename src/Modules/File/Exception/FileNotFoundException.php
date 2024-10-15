<?php declare(strict_types=1);

namespace Document\Modules\File\Exception;

use Document\Modules\General\Exception\AppException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class FileNotFoundException extends AppException implements HttpExceptionInterface
{
    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    /**
     * @return array<string>
     */
    public function getHeaders(): array
    {
        return [];
    }
}
