<?php
/**
 * リクエストエンティティ
 * クライアントから送信されてきたリクエストを保持する
 *
 * @author keita-nishimoto
 * @since 2016-09-12
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Domain;

use Illuminate\Http\Request;

/**
 * Class RequestEntity
 *
 * @category laravel-api-sample
 * @package Domain
 * @author keita-nishimoto
 * @since 2016-09-12
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class RequestEntity
{
    /**
     * リクエストID（UUID形式）
     *
     * @var string
     */
    private $XRequestId;

    /**
     * \Illuminate\Http\Request
     *
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * URLパラメータ
     *
     * @var array
     */
    private $urlParams;

    /**
     * リクエストパラメータ
     *
     * @var array
     */
    private $requestParams;

    /**
     * RequestEntity constructor.
     *
     * @param string $XRequestId
     * @param \Illuminate\Http\Request $request
     * @param array $urlParams
     */
    public function __construct(
        string $XRequestId,
        Request $request,
        array $urlParams = []
    ) {
        $this->setXRequestId($XRequestId)
            ->setRequest($request)
            ->setUrlParams($urlParams)
            ->createRequestParams();
    }

    /**
     * @return string
     */
    public function getXRequestId(): string
    {
        return $this->XRequestId;
    }

    /**
     * @param string $XRequestId
     * @return RequestEntity
     */
    private function setXRequestId(string $XRequestId): self
    {
        $this->XRequestId = $XRequestId;

        return $this;
    }

    /**
     * @return \Illuminate\Http\Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return RequestEntity
     */
    private function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return array
     */
    public function getUrlParams()
    {
        return $this->urlParams;
    }

    /**
     * @param array $urlParams
     * @return RequestEntity
     */
    private function setUrlParams(array $urlParams): self
    {
        $this->urlParams = $urlParams;

        return $this;
    }

    /**
     * @return array
     */
    public function getRequestParams(): array
    {
        return $this->requestParams;
    }

    /**
     * @param array $requestParams
     * @return RequestEntity
     */
    private function setRequestParams(array $requestParams): self
    {
        $this->requestParams = $requestParams;

        return $this;
    }

    /**
     * 全リクエストパラメータを生成する
     *
     * @return RequestEntity
     */
    private function createRequestParams(): self
    {
        $allParams = $this->getRequest()->all();
        $urlParams = $this->getUrlParams();

        $requestParams = array_merge($allParams, $urlParams);
        $this->setRequestParams($requestParams);

        return $this;
    }
}
