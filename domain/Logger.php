<?php
/**
 * ロギング
 * ドメイン層のロギングを行う
 *
 * @author keita-nishimoto
 * @since 2016-10-25
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 * @todo 20161025 まだまだ試行錯誤が必要、特にログにどのような情報を出すかは擬似的に運用してみないと決めにくい @keita-nishimoto
 */

namespace Domain;

/**
 * Class Logger
 *
 * @category laravel-api-sample
 * @package Domain
 * @author keita-nishimoto
 * @since 2016-10-25
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class Logger
{

    /**
     * info
     * APIのリクエストのログを出力するのに利用
     *
     * @param ResponseEntity $responseEntity
     */
    public static function info(ResponseEntity $responseEntity)
    {
        \Log::info(
            $responseEntity->getRequestEntity()->getXRequestId(),
            [
                'client_ip'      => $responseEntity->getRequestEntity()->getRequest()->getClientIp(),
                'user_agent'     => $responseEntity->getRequestEntity()->getRequest()->server('HTTP_USER_AGENT'),
                'request_params' => $responseEntity->getRequestEntity()->getRequestParams(),
                'response_body'  => $responseEntity->getBody(),
            ]
        );
    }

    /**
     * error
     * ビジネスロジックでエラーが起こった際に利用
     *
     * @param \Exception $e
     * @param ResponseEntity $responseEntity
     */
    public static function error(\Exception $e, ResponseEntity $responseEntity)
    {
        \Log::error(
            $e,
            [
                'x_request_id'   => $responseEntity->getRequestEntity()->getXRequestId(),
                'file'           => $e->getFile(),
                'line'           => $e->getLine(),
                'request_params' => $responseEntity->getRequestEntity()->getRequestParams()
            ]
        );
    }

    /**
     * critical
     * 予期しないエラーが発生した際に使用
     *
     * @param \Throwable $e
     * @param ResponseEntity $responseEntity
     */
    public static function critical(\Throwable $e, ResponseEntity $responseEntity)
    {
        \Log::critical(
            $e,
            [
                'x_request_id'   => $responseEntity->getRequestEntity()->getXRequestId(),
                'file'           => $e->getFile(),
                'line'           => $e->getLine(),
                'request_params' => $responseEntity->getRequestEntity()->getRequestParams()
            ]
        );
    }
}
