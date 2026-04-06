<?php

namespace App\Exceptions;

use App\Helpers\ApiResponse;
use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class ApiExceptionHandler
{
    public function handle(Throwable $e, $request)
    {
        if (! $request->expectsJson()) {
            return null;
        }

        if ($e instanceof ApiBusinessException) {
            return ApiResponse::sendResponse(
                $e->status(),
                $e->getMessage(),
                $e->data()
            );
        }

        if ($e instanceof ValidationException) {
            return ApiResponse::sendResponse(
                422,
                __('validation.validation-error'),
                $e->errors()
            );
        }

        if ($e instanceof AuthenticationException) {
            return ApiResponse::sendResponse(
                401,
                __('validation.unauthenticated'),
                []
            );
        }

        if ($e instanceof NotFoundHttpException) {
            return ApiResponse::sendResponse(
                404,
                __('validation.resource-not-found'),
                []
            );
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return ApiResponse::sendResponse(
                405,
                __('front.method-not-allowed'),
                []
            );
        }

        return ApiResponse::sendResponse(
            500,
            __('validation.server-error'),
            config('app.debug') ? ['error' => $e->getMessage()] : []
        );
    }
}
