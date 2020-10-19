<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['onKernelException', 60]
            ],
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $debug = $_ENV["APP_DEBUG"] !== false;

        $exception = $event->getThrowable();
        $code = Response::HTTP_INTERNAL_SERVER_ERROR;

        $response = new JsonResponse([
            "message" => $debug ? $exception->getMessage() : "Internal trouble. Someone got work to do.",
            "code" => $code
        ]);

        if ($exception instanceof AccessDeniedHttpException) {
            $response->setStatusCode(Response::HTTP_FORBIDDEN);

        } else if ($exception instanceof HttpExceptionInterface) {

            $response = new JsonResponse([
                "message" => $exception->getMessage(),
                "code" => $exception->getStatusCode()
            ]);

            $response->headers->replace($exception->getHeaders());

        } else {
            $response->setStatusCode($exception->getCode() ? $exception->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $event->setResponse($response);
    }
}
