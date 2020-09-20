<?php

namespace App\Exceptions;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use TypeError;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof TypeError) {
            return response()->json([
                'error' => 'Wrong type of parameters'
            ], 400);
        }
        if ($exception instanceof InvalidFormatException){
            return response()->json([
                'error' => 'Wrong date format'
            ], 400);
        }
        if ($exception instanceof HttpException){
            return response()->json([
                'error' => $exception->getMessage()
            ], $exception->getStatusCode());
        }
        return parent::render($request, $exception);
    }
}
