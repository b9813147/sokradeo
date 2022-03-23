<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Config;
use App\Helpers\Api\Response as ApiResponse;
use Throwable;


class Handler extends ExceptionHandler
{
    use ApiResponse;

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
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $exception
     * @return void
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Throwable $exception
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        return (preg_match('/application\/vnd\.sokradeo\.v\d+\+json/', $request->header('Accept', null)))
            ? $this->responseForApi($request, $exception)
            : $this->responseForWeb($request, $exception);

        //------------------------------------------
        return parent::render($request, $exception);
    }

    /**
     * Render an exception into an HTTP response for api.
     *
     * @param  \Illuminate\Http\Request  $req
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    private function responseForApi($req, $e) {

        $type = get_class($e);
        $resp = null;

        if (Config::get('app.env') === 'local') {
            $resp = parent::render($req, $e);
            return $resp;
        }

        switch ($type) {
            default:
                $resp = $this->error(['message' => $e->getMessage()]);
        }

        //$resp = parent::render($req, $e);

        return $resp;
    }

    /**
     * Render an exception into an HTTP response for web.
     *
     * @param  \Illuminate\Http\Request  $req
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    private function responseForWeb($req, $e) {

        $type = get_class($e);
        $data = ['status' => false, 'message' => $e->getMessage(), 'description' => ''];

        switch ($type) {

        }

        return $req->ajax() ? response()->json($data) : response()->view('app.errors.common', $data);
    }
}
