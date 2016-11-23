<?php
/**
 * APIのレスポンスを表すオブジェクト
 *
 * @author keita-nishimoto
 * @since 2016-09-12
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Domain;

/**
 * Class ResponseEntity
 *
 * @category laravel-api-sample
 * @package Domain
 * @author keita-nishimoto
 * @since 2016-09-12
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class ResponseEntity
{
    /**
     * ResponseEntity
     *
     * @var \Domain\RequestEntity
     */
    private $requestEntity;

    /**
     * HTTPステータスコード
     *
     * @var int
     */
    private $httpStatusCode;

    /**
     * HTTP header
     *
     * @var array
     */
    private $header;

    /**
     * 任意で返却するレスポンスヘッダ
     *
     * @var array
     */
    private $optionalHeader;

    /**
     * レスポンスBody
     *
     * @var array
     */
    private $body;

    /**
     * APIの実行結果
     *
     * @var array
     */
    private $apiResponse;

    /**
     * エラーコード
     *
     * @var int
     */
    private $errorCode;

    /**
     * エラーメッセージ
     *
     * @var string
     */
    private $errorMessage;

    /**
     * バリデーションエラー詳細
     *
     * @var array
     */
    private $validationErrors;

    /**
     * ResponseEntity constructor.
     *
     * @param RequestEntity $requestEntity
     */
    public function __construct(RequestEntity $requestEntity)
    {
        $this->setRequestEntity($requestEntity)
            ->setHttpStatusCode(200)
            ->setHeader([])
            ->setOptionalHeader([])
            ->setBody([])
            ->setApiResponse([])
            ->setErrorCode(0)
            ->setErrorMessage('')
            ->setHeader([])
            ->setValidationErrors([]);
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
     * @return ResponseEntity
     */
    private function setRequestEntity(RequestEntity $requestEntity): self
    {
        $this->requestEntity = $requestEntity;

        return $this;
    }

    /**
     * @return int
     */
    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    /**
     * @param int $httpStatusCode
     * @return ResponseEntity
     */
    public function setHttpStatusCode(int $httpStatusCode): self
    {
        $this->httpStatusCode = $httpStatusCode;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeader(): array
    {
        return $this->header;
    }

    /**
     * @param array $header
     * @return ResponseEntity
     */
    private function setHeader(array $header): self
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptionalHeader(): array
    {
        return $this->optionalHeader;
    }

    /**
     * @param array $optionalHeader
     * @return ResponseEntity
     */
    public function setOptionalHeader(array $optionalHeader): self
    {
        $this->optionalHeader = $optionalHeader;

        return $this;
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * @param array $body
     * @return ResponseEntity
     */
    private function setBody(array $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return array
     */
    public function getApiResponse(): array
    {
        return $this->apiResponse;
    }

    /**
     * @param array $apiResponse
     * @return ResponseEntity
     */
    public function setApiResponse(array $apiResponse): self
    {
        $this->apiResponse = $apiResponse;

        return $this;
    }

    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * @param int $errorCode
     * @return ResponseEntity
     */
    public function setErrorCode(int $errorCode): self
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     * @return ResponseEntity
     */
    public function setErrorMessage(string $errorMessage): self
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    /**
     * @return array
     */
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    /**
     * @param array $validationErrors
     * @return ResponseEntity
     */
    public function setValidationErrors(array $validationErrors): self
    {
        $this->validationErrors = $validationErrors;

        return $this;
    }

    /**
     * レスポンス用のHTTPヘッダを生成する
     *
     * @return ResponseEntity
     */
    private function createHeader(): self
    {
        $optionalHeader = $this->getOptionalHeader();

        if (empty($optionalHeader) === false) {
            foreach ($optionalHeader as $key => $value) {
                $header[$key] = $value;
            }
        }

        $header['X-Request-Id'] = $this->getRequestEntity()->getXRequestId();

        $this->setHeader($header);

        return $this;
    }

    /**
     * 正常レスポンス生成
     *
     * @return ResponseEntity
     */
    public function createSuccessResponse(): self
    {
        $this->createHeader();

        $body = $this->getApiResponse();
        $this->setBody($body);

        return $this;
    }

    /**
     * エラーレスポンス生成
     *
     * @return $this
     */
    public function createErrorResponse()
    {
        $this->convertHttpStatusCodeFromErrorCodeIfNeeded();
        $this->createHeader();

        $body = [
            'code'    => $this->getErrorCode(),
            'message' => $this->getErrorMessage(),
        ];

        $validationErrors = $this->getValidationErrors();
        if (empty($validationErrors) === false) {
            $body['errors'] = $validationErrors;
        }

        $this->setBody($body);

        return $this;
    }

    /**
     * エラーコードからHTTPステータスコードを変換する
     * 既にセットされているHTTPステータスコードよりもこちらの設定を優先する
     * 対応するHTTPステータスコードが存在しない場合は何もしない
     *
     * @return ResponseEntity
     */
    private function convertHttpStatusCodeFromErrorCodeIfNeeded(): self
    {
        $errorCode      = $this->getErrorCode();
        $httpStatusKey  = 'http_statuses' . '.' . $errorCode;
        $httpStatusCode = \Config::get($httpStatusKey);

        if (is_int($httpStatusCode) === true) {
            $this->setHttpStatusCode($httpStatusCode);
        }

        return $this;
    }
}
