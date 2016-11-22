<?php
/**
 * アカウント作成テストクラス
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
        $email    = 'email-update-apply-test-success-update@gmail.com';
        $password = 'Password1';

        $jsonResponse = $this->post(
            "/v1/accounts",
            [
                'email'    => $email,
                'password' => $password,
            ]
        );

        $responseArray = json_decode(
            $jsonResponse->response->content()
        );

        $expectedSub   = 1;
        $accountStatus = 0;

        $expectedLinks = [
            'self' => [
                'href' => "/v1/accounts",
            ]
        ];

        $expectedEmbedded = [
            '_embedded' => [
                'email'          => 'keita-nishimoto',
                'email_verified' => 1,
            ],
        ];

        $jsonResponse
            ->seeJson(['_links' => $expectedLinks])
            ->seeJson(['sub' => $expectedSub])
            ->seeJson(['_embedded' => $expectedEmbedded])
            ->seeStatusCode(201)
            ->seeHeader('X-Request-Id')
            ->seeHeader(
                'location',
                "https://dev.laravel-api.net/v1/accounts/$expectedSub"
            );

        $idSequence = 1;

        $this->seeInDatabase(
            'accounts',
            [
                'id'           => $expectedSub,
                'status'       => $accountStatus,
                'lock_version' => 0,
            ]
        );

        $this->seeInDatabase(
            'accounts_emails',
            [
                'id'           => $idSequence,
                'account_id'   => $expectedSub,
                'lock_version' => 0,
            ]
        );

        $this->seeInDatabase(
            'accounts_passwords',
            [
                'id'           => $idSequence,
                'account_id'   => $expectedSub,
                'lock_version' => 0,
            ]
        );
    }

    /**
     * 異常系テスト
     * メールアドレスが既に登録されている
     */
    public function testFailEmailIsAlreadyRegistered()
    {
        $email    = 'account-create-test-duplicated@gmail.com';
        $password = 'Password1';

        $jsonResponse = $this->post(
            "/v1/accounts",
            [
                'email'    => $email,
                'password' => $password,
            ]
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