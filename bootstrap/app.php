<?php

use App\Http\Middleware\EnsureOrganizationHasSubscription;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        \App\Providers\AliasResolverServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\SetUserLocale::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'organization-assigned' => \App\Http\Middleware\EnsureOrganizationAssigned::class,
            'active-subscription' => EnsureOrganizationHasSubscription::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'stripe/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (Symfony\Component\HttpFoundation\Response $response, Throwable $exception, Request $request) {
            $isServerError = in_array($response->getStatusCode(), [500, 503], true);
            $isInertia = $request->headers->get('X-Inertia') === 'true';
            // When in an Inertia request, we don't want to show the default error modal
            if ($isServerError && $isInertia) {
                $errorMessage = 'An internal error occurred, please try again. If the problem persists, please contact support.';
                // In local environment let's show the actual exception class & message
                if (App::hasDebugModeEnabled()) {
                    $errorMessage .= sprintf("\n%s: %s", get_class($exception), $exception->getMessage());
                }
                return response()->json([
                    'error_message' => $errorMessage,
                ], $response->getStatusCode());
            }

            if ($response->getStatusCode() === 419) {
                return back()->with([
                    'flash.banner' => 'The page expired, please try again.',
                ]);
            }

            return $response;
        });
    })->create();
