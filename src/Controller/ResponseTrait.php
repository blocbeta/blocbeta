<?php

namespace App\Controller;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method json($data, int $status = 200, array $headers = [], array $context = []): JsonResponse
 */
trait ResponseTrait
{
    protected function resourceNotFoundResponse(string $name = null, string $id = null)
    {
        $message = "Resource not found";

        if ($name && $id) {
            $message = "{$name} '$id' not found'";
        }

        if ($name && !$id) {
            $message = "{$name} not found'";
        }

        return $this->json([
            "type" => "notFound",
            "message" => $message,
            "code" => Response::HTTP_NOT_FOUND,
        ], Response::HTTP_NOT_FOUND);
    }

    protected function okResponse($data)
    {
        return $this->json($data, Response::HTTP_OK);
    }


    protected function createdResponse($data)
    {
        return $this->json($data, Response::HTTP_CREATED);
    }

    protected function internalErrorResponse()
    {
        return $this->json([
            "type" => "internalError",
            "message" => "Internal trouble. Someone got work to do.",
            "code" => Response::HTTP_INTERNAL_SERVER_ERROR,
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    protected function logicErrorResponse(string $message)
    {
        return $this->json([
            "type" => "logicError",
            "message" => $message,
            "code" => Response::HTTP_NOT_ACCEPTABLE,
        ], Response::HTTP_NOT_ACCEPTABLE);
    }

    protected function badRequestResponse(string $message)
    {
        return $this->json([
            "type" => "formError",
            "message" => $message,
            "code" => Response::HTTP_BAD_REQUEST,
        ], Response::HTTP_BAD_REQUEST);
    }

    protected function unauthorizedResponse()
    {
        return $this->json([
            "type" => "unauthorized",
            "message" => "Access denied.",
            "code" => Response::HTTP_FORBIDDEN,
        ], Response::HTTP_FORBIDDEN);
    }

    protected function badFormRequestResponse(FormInterface $form)
    {
        return $this->json([
            "type" => "formError",
            "message" => "Request payload validation failed",
            "code" => Response::HTTP_BAD_REQUEST,
            "errors" => FormErrorTrait::getFormErrors($form),
        ], Response::HTTP_BAD_REQUEST);
    }

    protected function noContentResponse()
    {
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}