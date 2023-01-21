<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
        // This will replace our 404 response with a JSON response.
        if ($exception instanceof ModelNotFoundException &&
            $request->wantsJson())
        {
            return response()->json([
                'data' => 'Record not found'
            ], 404);
        }

        else if ($exception instanceof ModelNotFoundException) {

            return response()->json(['errors'=>["noUser" => 'Entry for '.str_replace('App\\Models\\', '', $exception->getModel()).' not found']], 422);  // 404 (later)
            
        }

        else if ($exception instanceof TokenMismatchException) {
            
            return redirect()->back()->withErrors([
                'sessionExpired' => ['Your session has been expired, please try again'],
            ], $exception->getStatusCode());

        }

        else if ($exception instanceof HttpException)  {
            return response()->json(['errors'=>["noPermission" => 'You dont have permission for this action, please reload']], 403); 
        }

        return parent::render($request, $exception);
    }
}
