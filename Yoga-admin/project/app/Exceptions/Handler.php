<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function render($request, Throwable $exception)
    {
        // Check if the exception is a 404 error (invalid URL)
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            // Redirect to the index page
            return redirect()->route('admin.login');  // Assuming 'index' is your route name for the index page
        }

        return parent::render($request, $exception);
    }


    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
