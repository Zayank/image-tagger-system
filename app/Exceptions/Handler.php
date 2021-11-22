<?php

namespace App\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
        if ($request->is('api/*')) {
            return response()->json([
                'error'   => true,
                'message' => 'Not authenticated'
            ], 401);
        }
    
        });

        //catch all remaining exceptions
        $this->renderable(function (Throwable $e, $request) {

            if (method_exists($e, 'getStatusCode')) {
                $statusCode = $e->getStatusCode();
            } else {
                $statusCode = 500;
            }

            

            $response = [];

            switch ($statusCode) {
                case 401:
                    $response['message'] = 'Unauthorized';
                    break;
                case 403:
                    $response['message'] = 'Forbidden';
                    break;
                case 404:
                    $response['message'] = 'Not Found';
                    break;
                case 405:
                    $response['message'] = 'Method Not Allowed';
                    break;
                case 422:
                    $response['message'] = $e->original['message'];
                    $response['errors'] = $e->original['errors'];
                    break;
                default:
                    $response['message'] = ($statusCode == 500) ? 'Whoops, looks like something went wrong' : $e->getMessage();
                    break;
            }

            if (config('app.debug')) {
                $response['trace'] = $e->getTrace();
                $response['code'] = $e->getCode();
            }

            $response['status'] = $statusCode;
            return response()->json(['error' => true, 'message' => $response['message']], $response['status']);
        });
   
    }
}
