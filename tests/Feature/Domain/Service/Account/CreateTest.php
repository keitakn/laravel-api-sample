<?php
/**
 * アカウント作成テストクラス
 *
 * @author keita-nishimoto
 * @since 2016-11-07
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Tests\Feature\Domain\Service\Account;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Artisan;
use Tests\AbstractTestCase;

/**
 * Class CreateTest
 *
 * @category laravel-api-sample
 * @package Tests\Domain\Service\Account
 * @author keita-nishimoto
 * @since 2016-11-07
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class CreateTest extends AbstractTestCase
{
    use WithoutMiddleware;

    /**
     * ユニットテストの初期化処理
     * テストの実行毎に実行される
     */
    public function setUp()
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => 'Tests\Feature\Domain\Service\Account\CreateTestSeeder']);
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
        $testResponse = $this->post(
            '/v1/accounts',
            [
                'email'    => $email,
                'password' => $password,
            ]
        );

        // APIの結果を一部利用したいのでjson_decode()でarrayに変換します
        $responseArray = $testResponse->json();

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
            'password_hash'  => $responseArray['_embedded']['password_hash'],
        ];

        // 実際にJSONResponseの中に自分が期待したデータが入っているか確認します
        $testResponse
            ->assertJson(['sub' => $expectedSub])
            ->assertJson(['account_status' => $accountStatusString])
            ->assertJson(['_links' => $expectedLinks])
            ->assertJson(['_embedded' => $expectedEmbedded])
            ->assertStatus(201)
            ->assertHeader('X-Request-Id')
            ->assertHeader(
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

        $this->assertDatabaseHas(
            'accounts',
            [
                'id'           => $expectedSub,
                'status'       => $accountStatusInt,
                'lock_version' => 0,
            ]
        );

        $this->assertDatabaseHas(
            'accounts_emails',
            [
                'id'             => $idSequence,
                'account_id'     => $expectedSub,
                'email'          => $email,
                'email_verified' => 0,
                'lock_version'   => 0,
            ]
        );

        $this->assertDatabaseHas(
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

        $testResponse = $this->post(
            '/v1/accounts',
            [
                'email'          => $email,
                'password'       => $password,
                'email_verified' => $emailVerified
            ]
        );

        // APIの結果を一部利用したいのでjson_decode()でarrayに変換します
        $responseArray = $testResponse->json();

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
            'password_hash'  => $responseArray['_embedded']['password_hash'],
        ];

        $testResponse
            ->assertJson(['sub' => $expectedSub])
            ->assertJson(['account_status' => $accountStatusString])
            ->assertJson(['_links' => $expectedLinks])
            ->assertJson(['_embedded' => $expectedEmbedded])
            ->assertStatus(201)
            ->assertHeader('X-Request-Id')
            ->assertHeader(
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

        $this->assertDatabaseHas(
            'accounts',
            [
                'id'           => $expectedSub,
                'status'       => $accountStatusInt,
                'lock_version' => 0,
            ]
        );

        $this->assertDatabaseHas(
            'accounts_emails',
            [
                'id'             => $idSequence,
                'account_id'     => $expectedSub,
                'email'          => $email,
                'email_verified' => $emailVerified,
                'lock_version'   => 0,
            ]
        );

        $this->assertDatabaseHas(
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
            '/v1/accounts',
            [
                'email'    => $email,
                'password' => $password,
            ]
        );

        $errorCode = 40000;
        $messageKey = 'error_messages' . '.' . $errorCode;
        $errorMessage = \Config::get($messageKey);

        $jsonResponse
            ->assertJson(['code' => $errorCode])
            ->assertJson(['message' => $errorMessage])
            ->assertStatus(409)
            ->assertHeader('X-Request-Id');
    }
}
