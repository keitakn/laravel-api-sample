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
        // リクエストで送信するパラメータを定義
        $email    = 'account-create-test-success-required-params@gmail.com';
        $password = 'Password1';

        // /v1/accountsに対してPOSTリクエストを送信、第2引数はパラメータを配列で渡します。
        $jsonResponse = $this->post(
            "/v1/accounts",
            [
                'email'    => $email,
                'password' => $password,
            ]
        );

        // APIの結果を一部利用したいのでjson_decode()でstdClassに変換します
        $responseObject = json_decode(
            $jsonResponse->response->content()
        );

        // APIの期待値を設定します。
        $expectedSub         = 2;
        $accountStatusString = 'enabled';
        $accountStatusInt    = 0;

        $expectedLinks = [
            'self' => [
                'href' => "/v1/accounts/$expectedSub",
            ]
        ];

        $expectedEmbedded = [
            'email'          => $email,
            'email_verified' => 0,
            'password_hash'  => $responseObject->_embedded->password_hash,
        ];

        // 実際にJSONResponseの中に自分が期待したデータが入っているか確認します
        $jsonResponse
            ->seeJson(['sub' => $expectedSub])
            ->seeJson(['account_status' => $accountStatusString])
            ->seeJson(['_links' => $expectedLinks])
            ->seeJson(['_embedded' => $expectedEmbedded])
            ->seeStatusCode(201)
            ->seeHeader('X-Request-Id')
            ->seeHeader(
                'location',
                "https://dev.laravel-api.net/v1/accounts/$expectedSub"
            );

        // パスワードハッシュが意図したロジックで実施されているか確認
        $this->assertTrue(
            password_verify(
                $password,
                $expectedEmbedded['password_hash']
            )
        );

        // DBのテーブルに意図した形でデータが入っているか確認します
        $idSequence = 2;

        $this->seeInDatabase(
            'accounts',
            [
                'id'           => $expectedSub,
                'status'       => $accountStatusInt,
                'lock_version' => 0,
            ]
        );

        $this->seeInDatabase(
            'accounts_emails',
            [
                'id'             => $idSequence,
                'account_id'     => $expectedSub,
                'email'          => $email,
                'email_verified' => 0,
                'lock_version'   => 0,
            ]
        );

        $this->seeInDatabase(
            'accounts_passwords',
            [
                'id'            => $idSequence,
                'account_id'    => $expectedSub,
                'password_hash' => $expectedEmbedded['password_hash'],
                'lock_version'  => 0,
            ]
        );
    }

    /**
     * 正常系テスト
     * email_verifiedを1で指定
     */
    public function testSuccessEmailVerifiedTrue()
    {
        $email         = 'account-create-test-success-email-verified-true@gmail.com';
        $password      = 'Password1';
        $emailVerified = 1;

        $jsonResponse = $this->post(
            "/v1/accounts",
            [
                'email'          => $email,
                'password'       => $password,
                'email_verified' => $emailVerified
            ]
        );

        $responseObject = json_decode(
            $jsonResponse->response->content()
        );

        $expectedSub         = 2;
        $accountStatusString = 'enabled';
        $accountStatusInt    = 0;

        $expectedLinks = [
            'self' => [
                'href' => "/v1/accounts/$expectedSub",
            ]
        ];

        $expectedEmbedded = [
            'email'          => $email,
            'email_verified' => $emailVerified,
            'password_hash'  => $responseObject->_embedded->password_hash,
        ];

        $jsonResponse
            ->seeJson(['sub' => $expectedSub])
            ->seeJson(['account_status' => $accountStatusString])
            ->seeJson(['_links' => $expectedLinks])
            ->seeJson(['_embedded' => $expectedEmbedded])
            ->seeStatusCode(201)
            ->seeHeader('X-Request-Id')
            ->seeHeader(
                'location',
                "https://dev.laravel-api.net/v1/accounts/$expectedSub"
            );

        $this->assertTrue(
            password_verify(
                $password,
                $expectedEmbedded['password_hash']
            )
        );

        $idSequence = 2;

        $this->seeInDatabase(
            'accounts',
            [
                'id'           => $expectedSub,
                'status'       => $accountStatusInt,
                'lock_version' => 0,
            ]
        );

        $this->seeInDatabase(
            'accounts_emails',
            [
                'id'             => $idSequence,
                'account_id'     => $expectedSub,
                'email'          => $email,
                'email_verified' => $emailVerified,
                'lock_version'   => 0,
            ]
        );

        $this->seeInDatabase(
            'accounts_passwords',
            [
                'id'            => $idSequence,
                'account_id'    => $expectedSub,
                'password_hash' => $expectedEmbedded['password_hash'],
                'lock_version'  => 0,
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
