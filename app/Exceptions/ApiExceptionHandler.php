<?php

namespace App\Exceptions;

use App\Helpers\ApiResponse;
use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class ApiExceptionHandler
{
    /**
     * Determine whether the request targets the API.
     *
     * Uses the URL prefix as the authoritative check, so it works
     * regardless of the Accept header the client sends.
     */
    protected function isApiRequest($request): bool
    {
        return $request->is('api/*') || $request->is('api');
    }

    public function handle(Throwable $e, $request)
    {
        // Only intercept API requests; let web requests fall through to
        // Laravel's default rendering (Blade error pages, redirects, etc.)
        if (! $this->isApiRequest($request)) {
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

        // ModelNotFoundException is wrapped inside NotFoundHttpException by
        // Laravel, but can also be thrown directly from services.
        if ($e instanceof ModelNotFoundException) {
            return ApiResponse::sendResponse(
                404,
                __('validation.resource-not-found'),
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

        if ($e instanceof AccessDeniedHttpException) {
            return ApiResponse::sendResponse(
                403,
                __('validation.forbidden'),
                []
            );
        }

        if ($e instanceof TooManyRequestsHttpException) {
            return ApiResponse::sendResponse(
                429,
                __('validation.throttled'),
                []
            );
        }

        // Catch-all: guaranteed JSON for any unhandled exception
        return ApiResponse::sendResponse(
            500,
            __('validation.server-error'),
            config('app.debug') ? ['error' => $e->getMessage()] : []
        );
    }
}
