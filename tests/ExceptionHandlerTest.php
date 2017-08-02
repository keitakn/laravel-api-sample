<?php
/**
 * 例外ハンドラのテストクラス
 * ここでは意図しないURLがリクエストされた時のテストを行う
 *
 * @author keita-nishimoto
 * @since 2016-10-24
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Tests;

/**
 * Class ExceptionHandlerTest
 *
 * @category laravel-api-sample
 * @package Tests
 * @author keita-nishimoto
 * @since 2016-10-24
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class ExceptionHandlerTest extends \Tests\AbstractTestCase
{
    /**
     * 異常系テスト
     * 指定したURLが存在しない
     */
    public function testFailNotFoundHttp()
    {
        $jsonResponse = $this->post(
            '/v2/no/no'
        );

        $errorCode = 404;
        $messageKey = 'error_messages' . '.' . $errorCode;
        $errorMessage = \Config::get($messageKey);

        $jsonResponse
            ->assertJson(['code' => $errorCode])
            ->assertJson(['message' => $errorMessage])
            ->assertStatus(404)
            ->assertHeader('X-Request-Id');
    }

    /**
     * 異常系テスト
     * 許可されていないHTTPメソッド
     */
    public function testFailMethodNotAllowedHttp()
    {
        $jsonResponse = $this->patch(
            '/v1/accounts'
        );

        $errorCode = 405;
        $messageKey = 'error_messages' . '.' . $errorCode;
        $errorMessage = \Config::get($messageKey);

        $jsonResponse
            ->assertJson(['code' => $errorCode])
            ->assertJson(['message' => $errorMessage])
            ->assertStatus(405)
            ->assertHeader('X-Request-Id');
    }
}
