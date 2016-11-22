<?php
/**
 * アカウント登録テストクラス
 *
 * @author keita-nishimoto
 * @since 2016-11-07
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Tests\Domain\Service\Account;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithoutMiddleware;

/**
 * Class CreateTest
 *
 * @category laravel-api-sample
 * @package Tests\Domain\Service\Account
 * @author keita-nishimoto
 * @since 2016-11-07
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class CreateTest extends \Tests\AbstractTestCase
{
    use WithoutMiddleware;

    /**
     * ユニットテストの初期化処理
     * テストの実行毎に実行される
     */
    public function setUp()
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'Tests\Domain\Service\Account\CreateTestSeeder']);
    }

    /**
     * 正常系テスト
     * 必須パラメータのみを設定
     */
    public function testSuccessRequiredParams()
    {
        $sub   = 1;
        $email = 'email-update-apply-test-success-update@gmail.com';

        $jsonResponse = $this->post(
            "/v1/accounts",
            ['email' => $email]
        );

        $responseArray = json_decode(
            $jsonResponse->response->content()
        );

        $emailVerifyToken = $responseArray->email_verify_token;
        $expiredOn        = $responseArray->expired_on;

        $expectedLinks = [
            'self' => [
                'href' => "/v1/accounts/$sub/emails/$emailVerifyToken",
            ]
        ];

        $jsonResponse
            ->seeJson(['_links' => $expectedLinks])
            ->seeJson(['email_verify_token' => $emailVerifyToken])
            ->seeJson(['expired_on' => $expiredOn])
            ->seeStatusCode(201)
            ->seeHeader('X-Request-Id')
            ->seeHeader(
                'location',
                "https://dev.laravel-api.net/v1/accounts/$sub/emails/$emailVerifyToken"
            );

        $idSequence = 1;

        $this->seeInDatabase(
            'email_verify_tokens',
            [
                'id'                 => $idSequence,
                'email_verify_token' => $emailVerifyToken,
                'sub'                => $sub,
                'email'              => $email,
                'is_verified'        => 0,
                'expired_on'         => $expiredOn,
                'lock_version'       => 0,
            ]
        );
    }

    /**
     * 異常系テスト
     * アカウント情報が存在しない
     */
    public function testFailAccountInfoDoseNotExist()
    {
        $sub   = 2;
        $email = 'email-update-apply-test-success@gmail.com';

        $jsonResponse = $this->post(
            "/v1/accounts",
            ['email' => $email]
        );

        $errorCode = 40004;
        $messageKey = 'error_messages' . '.' . $errorCode;
        $errorMessage = \Config::get($messageKey);

        $jsonResponse
            ->seeJson(['code' => $errorCode])
            ->seeJson(['message' => $errorMessage])
            ->seeStatusCode(404)
            ->seeHeader('X-Request-Id');
    }

    /**
     * 異常系テスト
     * 退会アカウント
     */
    public function testFailCanceledAccount()
    {
        $sub   = 3;
        $email = 'email-update-apply-test-success@gmail.com';

        $jsonResponse = $this->post(
            "/v1/accounts",
            ['email' => $email]
        );

        $errorCode = 40008;
        $messageKey = 'error_messages' . '.' . $errorCode;
        $errorMessage = \Config::get($messageKey);

        $jsonResponse
            ->seeJson(['code' => $errorCode])
            ->seeJson(['message' => $errorMessage])
            ->seeStatusCode(403)
            ->seeHeader('X-Request-Id');
    }

    /**
     * 異常系テスト
     * メールアドレスが既に登録されている
     */
    public function testFailEmailIsAlreadyRegistered()
    {
        $sub   = 5;
        $email = 'email-update-apply-test-duplicated@gmail.com';

        $jsonResponse = $this->post(
            "/v1/accounts/$sub/emails",
            ['email' => $email]
        );

        $errorCode = 40000;
        $messageKey = 'error_messages' . '.' . $errorCode;
        $errorMessage = \Config::get($messageKey);

        $jsonResponse
            ->seeJson(['code' => $errorCode])
            ->seeJson(['message' => $errorMessage])
            ->seeStatusCode(409)
            ->seeHeader('X-Request-Id');
    }
}
