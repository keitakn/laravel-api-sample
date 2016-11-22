<?php

namespace App\Exceptions;

use Domain\Logger;
use Exception;
use Exceptions\DomainException;
use Factories\EntityFactory;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Infrastructures\Utility\StringUtility;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
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
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            $responseEntity = $this->convertNotFoundHttpExceptionToResponse($request, $exception);

            Logger::error($exception, $responseEntity);

            return response()->json(
                $responseEntity->getBody(),
                $responseEntity->getHttpStatusCode(),
                $responseEntity->getHeader()
            );
        }

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
            $responseEntity = $this->convertMethodNotAllowedHttpExceptionToResponse($request, $exception);

            Logger::error($exception, $responseEntity);

            return response()->json(
                $responseEntity->getBody(),
                $responseEntity->getHttpStatusCode(),
                $responseEntity->getHeader()
            );
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }

    /**
     * 指定したURLが見つからない場合のレスポンス
     *
     * @param \Illuminate\Http\Request $request
     * @return \Domain\ResponseEntity
     */
    private function convertNotFoundHttpExceptionToResponse(
        \Illuminate\Http\Request $request,
        Exception $exception
    ) {
        $requestEntity = $this->createRequestEntity($request);

        $domainException = new DomainException(404, $exception);

        $responseEntity = EntityFactory::createResponseEntity($requestEntity);
        $responseEntity
            ->setErrorCode($domainException->getErrorCode())
            ->setErrorMessage($domainException->getErrorMessage())
            ->createErrorResponse();

        return $responseEntity;
    }

    /**
     * 許可されていないHTTPメソッドの場合のレスポンス
     *
     * @param \Illuminate\Http\Request $request
     * @param Exception $exception
     * @return \Domain\ResponseEntity
     */
    private function convertMethodNotAllowedHttpExceptionToResponse(
        \Illuminate\Http\Request $request,
        Exception $exception
    ) {
        $requestEntity = $this->createRequestEntity($request);

        $domainException = new DomainException(405, $exception);

        $responseEntity = EntityFactory::createResponseEntity($requestEntity);
        $responseEntity
            ->setErrorCode($domainException->getErrorCode())
            ->setErrorMessage($domainException->getErrorMessage())
            ->createErrorResponse();

        return $responseEntity;
    }

    /**
     * リクエストEntityを生成する
     *
     * @param \Illuminate\Http\Request $request
     * @return \Domain\RequestEntity
     */
    private function createRequestEntity(\Illuminate\Http\Request $request)
    {
        $traceId = StringUtility::generateUuid();

        $requestEntity = EntityFactory::createRequestEntity(
            $traceId,
            $request
        );

        return $requestEntity;
    }
}
