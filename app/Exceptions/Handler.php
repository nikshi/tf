<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->wantsJson() || $request->isJson()) {
            $status = 400;
            $response = [
                'success' => false
            ];

            if($exception instanceof ValidationException) {
                $response['validatorErrors'] = $exception->errors();
            }

            if (config('app.debug')) {
                $response['exception'] = get_class($exception);
                $response['message'] = $exception->getMessage();
                $response['trace'] = $exception->getTrace();
            }

            if ($this->isHttpException($exception)) {
                $status = $exception->getCode();
            }

            return response()->json($response, $status);
        }

        return parent::render($request, $exception);
    }
}
