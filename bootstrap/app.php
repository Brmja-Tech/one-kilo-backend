<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',

        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/dashboard.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Force Accept: application/json on every /api/* request so that
        // expectsJson() is always true, preventing HTML responses, redirects,
        // and null exception-handler returns for mobile clients.
        $middleware->prependToGroup('api', [
            \App\Http\Middleware\ForceJsonResponse::class,
        ]);

        $middleware->redirectGuestsTo(function () {
            // API requests must NEVER redirect — the ForceJsonResponse
            // middleware already sets Accept: application/json, so
            // Laravel's Authenticate middleware will throw an
            // AuthenticationException (rendered as 401 JSON by our
            // ApiExceptionHandler) instead of following this redirect.
            // This guard is a safety net in case the middleware is
            // bypassed for any reason.
            if (request()->is('api/*') || request()->is('api')) {
                return null;
            }

            if (request()->is('*/dashboard/*')) {
                return route('dashboard.login');
            } else {
                return route('login');
            }
        });
        $middleware->redirectUsersTo(function () {
            if (Auth::guard('admin')->check()) {
                return route('dashboard.home');
            } else {
                return route('home');
            }
        });
        $middleware->alias([
            /**** OTHER MIDDLEWARE ALIASES ****/
            'localize'                => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect'    => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect'   => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeCookieRedirect'    => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
            'localeViewPath'          => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, $request) {
            return (new \App\Exceptions\ApiExceptionHandler)->handle($e, $request);
        });
    })->create();
