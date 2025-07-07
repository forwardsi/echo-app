<?php

namespace App\Exceptions;

use App\Notifications\ErrorOccurredNotification;
use Illuminate\Support\Facades\Notification;
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

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        $test = true; // Change to true/false for testing

        if ($test == false) {
            \Log::error($exception->getMessage());
            Notification::route('mail', 'tilen@forward.si')
                ->notify(new ErrorOccurredNotification($exception->getMessage()));

            return response()->view('error', [
                'errorMessage' => $exception->getMessage()
            ], 500);
        }

        // Always return the default Laravel error handling if testing is enabled
        return parent::render($request, $exception);
    }

}
