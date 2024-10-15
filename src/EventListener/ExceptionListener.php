<?php declare(strict_types=1);

namespace Document\EventListener;

use Document\Modules\General\Exception\AppException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Throwable;

#[AsEventListener(event: 'kernel.exception')]
class ExceptionListener
{
    public function __construct() {}

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $response = new Response();
        if (!$exception instanceof AppException) { // handling only our defined exceptions
            return;
        }

        // Customize your response object to display the exception details
        try {
            $response->setStatusCode($exception->getStatusCode());
            $response->setContent($this->format($exception));
        } catch (Throwable $e) {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        } finally {
            $event->setResponse($response); // sends the modified response object to the event
        }
    }

    public function format(Throwable $e): string
    {
        return (string) json_encode([
            'title' => $e->getMessage(),
            'status' => $e instanceof AppException ? $e->getStatusCode() : $e->getCode(),
        ]);
    }
}
