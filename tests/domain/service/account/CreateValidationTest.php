<?php
/**
 * アカウント作成バリデーションテストクラス
 *
 * @author keita-nishimoto
 * @since 2016-11-17
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Tests\Domain\Service\Account;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\ValidationProviderCreator;

/**
 * Class CreateValidationTest
 *
 * @category laravel-api-sample
 * @package Tests\Domain\Service\Account
 * @author keita-nishimoto
 * @since 2016-11-17
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class CreateValidationTest extends \Tests\AbstractTestCase
{
    use WithoutMiddleware;

    /**
     * 全パラメータのバリデーションエラー
     *
     * @dataProvider allParamsProvider
     * @param $email
     * @param $emailVerified
     * @param $password
     */
    public function testAllParams($email, $emailVerified, $password)
    {
        $jsonResponse = $this->post(
            "/v1/accounts",
            [
                'email'          => $email,
                'password'       => $password,
                'email_verified' => $emailVerified,
            ]
        );

        $errorCode = 422;
        $messageKey = 'error_messages' . '.' . $errorCode;
        $errorMessage = \Config::get($messageKey);

        $jsonResponse
            ->seeJson(['code' => $errorCode])
            ->seeJson(['message' => $errorMessage])
            ->seeStatusCode(422)
            ->seeHeader('X-Request-Id');

        $responseStdObject = json_decode(
            $jsonResponse->response->content()
        );

        $this->assertObjectHasAttribute('email', $responseStdObject->errors);
        $this->assertObjectHasAttribute('email_verified', $responseStdObject->errors);
    }

    /**
     * 全パラメータ用のデータプロバイダー
     *
     * @return array
     */
    public function allParamsProvider()
    {
        return [
            'マルチバイト文字' => [
                'あいうえお',
                'あいうえお',
                'あいうえお',
            ],
            '記号だけ' => [
                '@@@',
                '+++',
                '---',
            ],
            'JSON' => [
                json_encode(
                    ['りんご', 'ばなな', 'みかん']
                ),
                json_encode(
                    ['りんご', 'ばなな', 'みかん']
                ),
                json_encode(
                    ['りんご', 'ばなな', 'みかん']
                ),
            ],
            '大きな数字' => [
                -9999999999,
                99999999999,
                99999999999,
            ],
            '大きな文字列' => [
                str_repeat('a@', 65),
                str_repeat('p1', 50),
                str_repeat('11', 2),
            ],
        ];
    }

    /**
     * メールアドレスのバリデーションエラー
     *
     * @dataProvider emailProvider
     * @param $email
     */
    public function testEmail($email)
    {
        $jsonResponse = $this->post(
            "/v1/accounts",
            [
                'email'    => $email,
                'password' => 'Password123',
            ]
        );

        $errorCode = 422;
        $messageKey = 'error_messages' . '.' . $errorCode;
        $errorMessage = \Config::get($messageKey);

        $jsonResponse
            ->seeJson(['code' => $errorCode])
            ->seeJson(['message' => $errorMessage])
            ->seeStatusCode(422)
            ->seeHeader('X-Request-Id');

        $responseStdObject = json_decode(
            $jsonResponse->response->content()
        );

        $this->assertObjectHasAttribute('email', $responseStdObject->errors);
        $this->assertObjectNotHasAttribute('password', $responseStdObject->errors);
        $this->assertObjectNotHasAttribute('email_verified', $responseStdObject->errors);
    }

    /**
     * メールアドレスのデータプロバイダ
     *
     * @return array
     */
    public function emailProvider()
    {
        return ValidationProviderCreator::emailIsRequiredParams();
    }

    /**
     * パスワードのバリデーションエラー
     *
     * @dataProvider passwordProvider
     * @param $password
     */
    public function testPassword($password)
    {
        $email = 'k-keita@example.com';

        $jsonResponse = $this->post(
            "/v1/accounts",
            [
                'email'    => $email,
                'password' => $password,
            ]
        );

        $errorCode = 422;
        $messageKey = 'error_messages' . '.' . $errorCode;
        $errorMessage = \Config::get($messageKey);

        $jsonResponse
            ->seeJson(['code' => $errorCode])
            ->seeJson(['message' => $errorMessage])
            ->seeStatusCode(422)
            ->seeHeader('X-Request-Id');

        $responseStdObject = json_decode(
            $jsonResponse->response->content()
        );

        $this->assertObjectNotHasAttribute('email', $responseStdObject->errors);
        $this->assertObjectHasAttribute('password', $responseStdObject->errors);
        $this->assertObjectNotHasAttribute('email_verified', $responseStdObject->errors);
    }

    /**
     * パスワードのデータプロバイダ
     *
     * @return array
     */
    public function passwordProvider()
    {
        return ValidationProviderCreator::passwordIsRequiredParams();
    }

    /**
     * email_verifiedのバリデーションエラー
     *
     * @dataProvider emailVerifiedProvider
     * @param $emailVerified
     */
    public function testEmailVerified($emailVerified)
    {
        $email         = 'k-keita@example.com';
        $password      = 'Password1';
        $emailVerified = $emailVerified;

        $jsonResponse = $this->post(
            "/v1/accounts",
            [
                'email'          => $email,
                'password'       => $password,
                'email_verified' => $emailVerified,
            ]
        );

        $errorCode = 422;
        $messageKey = 'error_messages' . '.' . $errorCode;
        $errorMessage = \Config::get($messageKey);

        $jsonResponse
            ->seeJson(['code' => $errorCode])
            ->seeJson(['message' => $errorMessage])
            ->seeStatusCode(422)
            ->seeHeader('X-Request-Id');

        $responseStdObject = json_decode(
            $jsonResponse->response->content()
        );

        $this->assertObjectNotHasAttribute('email', $responseStdObject->errors);
        $this->assertObjectNotHasAttribute('password', $responseStdObject->errors);
        $this->assertObjectHasAttribute('email_verified', $responseStdObject->errors);
    }

    /**
     * email_verifiedのデータプロバイダ
     *
     * @return array
     */
    public function emailVerifiedProvider()
    {
        return ValidationProviderCreator::emailVerifiedIsOptionalParams();
    }
}
