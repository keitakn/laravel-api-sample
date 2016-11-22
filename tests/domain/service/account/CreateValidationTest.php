<?php
/**
 * アカウント登録バリデーションテストクラス
 *
 * @author keita-nishimoto
 * @since 2016-11-17
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Tests\Domain\Service\Account;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\ValidationProviderCreator;

/**
 * Class EmailUpdateApplyValidationTest
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
     * 必須パラメータのバリデーションエラー
     *
     * @dataProvider requiredParamsProvider
     * @param $sub
     * @param $email
     */
    public function testRequiredParams($sub, $email)
    {
        $jsonResponse = $this->post(
            "/v1/accounts/$sub/emails",
            ['email' => $email]
        );

        $errorCode = 11000;
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

        $this->assertObjectHasAttribute('sub', $responseStdObject->errors);
        $this->assertObjectHasAttribute('email', $responseStdObject->errors);
    }

    /**
     * 必須パラメータ用のデータプロバイダー
     *
     * @return array
     */
    public function requiredParamsProvider()
    {
        // ユーザーID、メールアドレスの順番
        return [
            'failParamIsMultiByteChars' => [
                'あいうえお',
                'あいうえお',
            ],
            '記号と.から始まるメールアドレス' => [
                'id-000',
                '.keita-koga@gmail.com',
            ],
            '半角英数とJSON' => [
                'abc123',
                json_encode(
                    ['りんご', 'ばなな', 'みかん']
                ),
            ],
            'bigInt' => [
                -9999999999,
                99999999999,
            ],
            'longString' => [
                str_repeat('q', 65),
                str_repeat('k@', 129),
            ],
        ];
    }

    /**
     * ユーザーIDのバリデーションエラー
     *
     * @dataProvider subProvider
     * @param $sub
     */
    public function testSub($sub)
    {
        $jsonResponse = $this->post(
            "/v1/accounts/$sub/emails",
            ['email' => 'keita-koga@gmail.com']
        );

        $errorCode = 11000;
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

        $this->assertObjectHasAttribute('sub', $responseStdObject->errors);
        $this->assertObjectNotHasAttribute('email', $responseStdObject->errors);
    }

    /**
     * ユーザーIDのデータプロバイダ
     *
     * @return array
     */
    public function subProvider()
    {
        return ValidationProviderCreator::createSubInRequiredParams();
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
            "/v1/accounts/999999/emails",
            ['email' => $email]
        );

        $errorCode = 11000;
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

        $this->assertObjectNotHasAttribute('sub', $responseStdObject->errors);
        $this->assertObjectHasAttribute('email', $responseStdObject->errors);
    }

    /**
     * メールアドレスのデータプロバイダ
     *
     * @return array
     */
    public function emailProvider()
    {
        return ValidationProviderCreator::createEmailInRequiredParams();
    }
}
