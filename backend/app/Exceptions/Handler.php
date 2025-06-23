<?php // app/Exceptions/Handler.php
namespace App\Exceptions;

use App\Http\Responses\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Exception to status code mapping
     */
    protected $exceptionStatusCodes = [
        AuthorizationException::class => 403,
        \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException::class => 403,

        AuthenticationException::class => 401,
        ValidationException::class => 422,
        ModelNotFoundException::class => 404,
        NotFoundHttpException::class => 404,
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Handle API-specific exceptions
        $this->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->handleApiException($e);
            }
        });
    }

    /**
     * Handle API exceptions and return standardized responses
     *
     * @param Throwable $exception
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleApiException(Throwable $exception)
    {
       // dd($exception);
        $statusCode = $this->getStatusCode($exception);

       $message = $this->getMessage($exception);
              // $erorr = $this->getErrors($exception);

        return ApiResponse::error($message,[], $statusCode);
    }

    /**
     * Get appropriate status code for the exception
     */
    private function getStatusCode(Throwable $exception): int
    {
        // Check our mapping first
        foreach ($this->exceptionStatusCodes as $exceptionType => $code) {
            if ($exception instanceof $exceptionType) {
                return $code;
            }
        }

        // For HttpException, use its status code
        if ($exception instanceof HttpException) {
            return $exception->getStatusCode();
        }

        // Default to 500 for unhandled exceptions
        return 500;
    }

    /**
     * Get appropriate message for the exception
     */
    private function getMessage(Throwable $exception): string
    {
       // dd($exception);

        // if ($exception instanceof AuthorizationException) {

        //     return 'You are not authorized to perform this action';
        // }

        // if ($exception instanceof AuthenticationException) {

        //     return 'Unauthenticated';
        // }

        // if ($exception instanceof ValidationException) {

        //     return 'Validation failed';
        // }

        if ($exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) {
         //   dd($exception);
            return 'The requested resource was not found';
        }

        // For production, hide detailed error messages
        if (app()->environment('production') && !($exception instanceof HttpException)) {
            return 'An unexpected error occurred';
        }

        // Return the exception message for other cases
        return $exception->getMessage() ?: 'An unexpected error occurred';
    }

    /**
     * Get errors for the exception
     */
    // private function getErrors(Throwable $exception): ?array
    // {
    //     // For validation exceptions, return validation errors
    //     if ($exception instanceof ValidationException) {
    //         return $exception->errors();
    //     }

    //     // For non-production environments, return debug information
    //     if (!app()->environment('production')) {
    //         return [
    //             'exception' => get_class($exception),
    //             'file' => $exception->getFile(),
    //             'line' => $exception->getLine(),
    //             'trace' => app()->environment('local') ? $exception->getTrace() : null
    //         ];
    //     }

    //     return null;
    // }
}
