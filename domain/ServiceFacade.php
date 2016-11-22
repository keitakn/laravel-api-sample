<?php
/**
 * ServiceFacade
 * アプリケーション層からドメイン層を呼び出す時に利用する
 *
 * @author keita-nishimoto
 * @since 2016-09-09
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Domain;

use Exceptions\DomainException;
use Exceptions\ValidationException;
use Factories\EntityFactory;
use Factories\ServiceFactory;
use Illuminate\Http\Request;
use Infrastructures\Utility\StringUtility;

/**
 * Class ServiceFacade
 *
 * @category laravel-api-sample
 * @package Domain
 * @author keita-nishimoto
 * @since 2016-09-09
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class ServiceFacade
{
    /**
     * \Illuminate\Http\Request
     *
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * リクエストEntity
     *
     * @var \Domain\RequestEntity
     */
    private $requestEntity;

    /**
     * サービス名
     *
     * @var string
     */
    private $serviceName;

    /**
     * 実行メソッド
     *
     * @var string
     */
    private $executeMethod;

    /**
     * URLパラメータ
     *
     * @var array
     */
    private $urlParams;

    /**
     * ServiceFacade constructor.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $serviceName
     * @param string $executeMethod
     * @param array $urlParams
     */
    public function __construct(
        Request $request,
        $serviceName = '',
        $executeMethod = '',
        $urlParams = []
    ) {
        $this->setRequest($request)
            ->setServiceName($serviceName)
            ->setExecuteMethod($executeMethod)
            ->setUrlParams($urlParams);
    }

    /**
     * 処理を実行する
     *
     * @return ResponseEntity
     */
    public function execute(): ResponseEntity
    {
        \DB::beginTransaction();

        try {
            $serviceInstance = ServiceFactory::create(
                $this->getServiceName(),
                $this->getExecuteMethod()
            );

            $XRequestId = StringUtility::generateUuid();

            $requestEntity = EntityFactory::createRequestEntity(
                $XRequestId,
                $this->getRequest(),
                $this->getUrlParams()
            );
            $this->setRequestEntity($requestEntity);

            $this->doValidate();

            $responseEntity = $this->executeMethod($serviceInstance);

            \DB::commit();

            return $responseEntity;
        } catch (ValidationException $e) {
            \DB::rollBack();

            $responseEntity = EntityFactory::createResponseEntity($requestEntity);
            $responseEntity
                ->setHttpStatusCode(422)
                ->setErrorCode($e->getErrorCode())
                ->setErrorMessage($e->getErrorMessage())
                ->setValidationErrors($e->getValidationErrors())
                ->createErrorResponse();

            Logger::error($e, $responseEntity);

            return $responseEntity;
        } catch (DomainException $e) {
            \DB::rollBack();

            $responseEntity = EntityFactory::createResponseEntity($requestEntity);
            $responseEntity
                ->setHttpStatusCode(400)
                ->setErrorCode($e->getErrorCode())
                ->setErrorMessage($e->getErrorMessage())
                ->createErrorResponse();

            Logger::error($e, $responseEntity);

            return $responseEntity;
        } catch (\Illuminate\Database\QueryException $e) {
            \DB::rollBack();

            // DBのDuplicate entryは特別なエラーコードを返す
            if ($e->getCode() === '23000') {
                $errorCode    = 409;
                $messageKey   = 'error_messages' . '.' . $errorCode;
                $errorMessage = \Config::get($messageKey);

                $responseEntity = EntityFactory::createResponseEntity($requestEntity);
                $responseEntity
                    ->setHttpStatusCode($errorCode)
                    ->setErrorCode($errorCode)
                    ->setErrorMessage($errorMessage)
                    ->createErrorResponse();

                Logger::error($e, $responseEntity);

                return $responseEntity;
            }

            $errorCode = 20001;
            $messageKey = 'error_messages' . '.' . $errorCode;
            $errorMessage = \Config::get($messageKey);

            $responseEntity = EntityFactory::createResponseEntity($requestEntity);
            $responseEntity
                ->setErrorCode($errorCode)
                ->setErrorMessage($errorMessage)
                ->createErrorResponse();

            Logger::critical($e, $responseEntity);

            return $responseEntity;
        } catch (\Throwable $e) {
            \DB::rollBack();

            $errorCode = 10000;
            $messageKey = 'error_messages' . '.' . $errorCode;
            $errorMessage = \Config::get($messageKey);

            $responseEntity = EntityFactory::createResponseEntity($requestEntity);
            $responseEntity
                ->setErrorCode($errorCode)
                ->setErrorMessage($errorMessage)
                ->createErrorResponse();

            Logger::critical($e, $responseEntity);

            return $responseEntity;
        } finally {
            Logger::info($responseEntity);
        }
    }

    /**
     * サービスメソッドを実行する
     *
     * @param $serviceInstance
     * @return ResponseEntity
     */
    private function executeMethod($serviceInstance): ResponseEntity
    {
        $executeMethod = $this->getExecuteMethod();

        return $serviceInstance->$executeMethod(
            $this->getRequestEntity()
        );
    }

    /**
     * バリデーションを実行する
     */
    private function doValidate()
    {
        $requestEntity = $this->getRequestEntity();
        $requestParams = $requestEntity->getRequestParams();

        $serviceName   = strtolower($this->getServiceName());
        $executeMethod = $this->getExecuteMethod();

        ValidationService::doValidate(
            $serviceName,
            $executeMethod,
            $requestParams
        );
    }

    /**
     * Accessor
     *
     * @return \Illuminate\Http\Request $request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return ServiceFacade
     */
    private function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return RequestEntity
     */
    public function getRequestEntity(): RequestEntity
    {
        return $this->requestEntity;
    }

    /**
     * @param RequestEntity $requestEntity
     * @return ServiceFacade
     */
    private function setRequestEntity(RequestEntity $requestEntity): self
    {
        $this->requestEntity = $requestEntity;

        return $this;
    }

    /**
     * @return string
     */
    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    /**
     * @param string $serviceName
     * @return ServiceFacade
     */
    private function setServiceName(string $serviceName): self
    {
        $this->serviceName = $serviceName;

        return $this;
    }

    /**
     * @return string
     */
    public function getExecuteMethod(): string
    {
        return $this->executeMethod;
    }

    /**
     * @param string $executeMethod
     * @return ServiceFacade
     */
    private function setExecuteMethod(string $executeMethod): self
    {
        $this->executeMethod = $executeMethod;

        return $this;
    }

    /**
     * @return array
     */
    public function getUrlParams(): array
    {
        return $this->urlParams;
    }

    /**
     * @param array $urlParams
     * @return ServiceFacade
     */
    private function setUrlParams(array $urlParams): self
    {
        $this->urlParams = $urlParams;

        return $this;
    }
}
