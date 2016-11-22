<?php
/**
 * アカウント登録テストデータ投入
 *
 * @author keita-nishimoto
 * @since 2016-11-07
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Tests\Domain\Service\Account;

use Factories\Account\ValueFactory;
use Illuminate\Database\Seeder;
use Infrastructures\Utility\StringUtility;

/**
 * Class EmailUpdateApplyTestSeeder
 *
 * @category laravel-api-sample
 * @package Tests\Domain\Service\Login
 * @author keita-nishimoto
 * @since 2016-11-07
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class CreateTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->truncate();
        \DB::table('users_names')->truncate();
        \DB::table('registers')->truncate();
        \DB::table('registers_tmp_values')->truncate();
        \DB::table('accounts')->truncate();
        \DB::table('accounts_emails')->truncate();
        \DB::table('accounts_passwords')->truncate();
        \DB::table('accounts_banned_emails')->truncate();
        \DB::table('email_verify_tokens')->truncate();

        $this->testSuccessRequiredParams();
        $this->testFailAccountInfoDoseNotExist();
        $this->testFailCanceledAccount();
        $this->testFailBannedAccount();
        $this->testFailEmailIsAlreadyRegistered();
        $this->testFailBannedEmail();
    }

    /**
     * 正常系テスト
     * 必須パラメータのみを設定
     */
    private function testSuccessRequiredParams()
    {
        $dateTime  = new \DateTime('now');
        $expiredOn = $dateTime->format('Y-m-d H:i:s');

        // 会員登録トークンはテストに直接関係ない値なので適当な値を生成する
        $uuid = StringUtility::generateUuid();
        $registerToken = hash('sha256', $uuid);

        $sub        = 1;
        $registerId = 1;
        $idSequence = 1;
        $email      = 'email-update-apply-test-success@gmail.com';

        $registers = [
            'id'             => $sub,
            'register_token' => $registerToken,
            'is_registered'  => 1,
            'expired_on'     => $expiredOn,
            'lock_version'   => 0,
        ];
        \DB::table('registers')->insert($registers);

        $users = [
            'id'          => $sub,
            'register_id' => $registerId,
        ];
        \DB::table('users')->insert($users);

        $names = [
            'id'          => $idSequence,
            'user_id'     => $sub,
            'given_name'  => '百合子',
            'family_name' => '小池',
        ];
        \DB::table('users_names')->insert($names);

        $accounts = [
            'id'          => $sub,
            'register_id' => $registerId,
        ];
        \DB::table('accounts')->insert($accounts);

        $accountsEmails = [
            'id'         => $idSequence,
            'account_id' => $sub,
            'email'      => $email,
        ];
        \DB::table('accounts_emails')->insert($accountsEmails);

        $passwordValue = ValueFactory::createPasswordValue(
            [
                'password'     => 'password',
                'passwordType' => 0,
            ]
        );

        $accountsPasswords = [
            'account_id'    => $sub,
            'password_hash' => $passwordValue->getPasswordHash(),
            'password_type' => $passwordValue->getPasswordType(),
        ];
        \DB::table('accounts_passwords')->insert($accountsPasswords);
    }

    /**
     * 異常系テスト
     * アカウント情報が存在しない
     */
    private function testFailAccountInfoDoseNotExist()
    {
        $dateTime  = new \DateTime('now');
        $expiredOn = $dateTime->format('Y-m-d H:i:s');

        // 会員登録トークンはテストに直接関係ない値なので適当な値を生成する
        $uuid = StringUtility::generateUuid();
        $registerToken = hash('sha256', $uuid);

        $sub        = 2;
        $registerId = 2;
        $idSequence = 2;

        $registers = [
            'id'             => $sub,
            'register_token' => $registerToken,
            'is_registered'  => 1,
            'expired_on'     => $expiredOn,
            'lock_version'   => 0,
        ];
        \DB::table('registers')->insert($registers);

        $users = [
            'id'          => $sub,
            'register_id' => $registerId,
        ];
        \DB::table('users')->insert($users);

        $names = [
            'id'          => $idSequence,
            'user_id'     => $sub,
            'given_name'  => '百合子',
            'family_name' => '小池',
        ];
        \DB::table('users_names')->insert($names);
    }

    /**
     * 異常系テスト
     * 退会アカウント
     */
    private function testFailCanceledAccount()
    {
        $dateTime  = new \DateTime('now');
        $expiredOn = $dateTime->format('Y-m-d H:i:s');

        // 会員登録トークンはテストに直接関係ない値なので適当な値を生成する
        $uuid = StringUtility::generateUuid();
        $registerToken = hash('sha256', $uuid);

        $sub        = 3;
        $registerId = 3;
        $idSequence = 3;
        $email      = 'email-update-apply-test-fail-canceled@gmail.com';

        $registers = [
            'id'             => $sub,
            'register_token' => $registerToken,
            'is_registered'  => 1,
            'expired_on'     => $expiredOn,
            'lock_version'   => 0,
        ];
        \DB::table('registers')->insert($registers);

        $users = [
            'id'          => $sub,
            'register_id' => $registerId,
        ];
        \DB::table('users')->insert($users);

        $names = [
            'id'          => $idSequence,
            'user_id'     => $sub,
            'given_name'  => '百合子',
            'family_name' => '小池',
        ];
        \DB::table('users_names')->insert($names);

        $accounts = [
            'id'          => $sub,
            'register_id' => $registerId,
            'status'      => 1,
        ];
        \DB::table('accounts')->insert($accounts);

        $accountsEmails = [
            'id'         => $idSequence,
            'account_id' => $sub,
            'email'      => $email,
        ];
        \DB::table('accounts_emails')->insert($accountsEmails);

        $passwordValue = ValueFactory::createPasswordValue(
            [
                'password'     => 'password',
                'passwordType' => 0,
            ]
        );

        $accountsPasswords = [
            'account_id'    => $sub,
            'password_hash' => $passwordValue->getPasswordHash(),
            'password_type' => $passwordValue->getPasswordType(),
        ];
        \DB::table('accounts_passwords')->insert($accountsPasswords);
    }

    /**
     * 異常系テスト
     * 強制退会アカウント
     */
    private function testFailBannedAccount()
    {
        $dateTime  = new \DateTime('now');
        $expiredOn = $dateTime->format('Y-m-d H:i:s');

        // 会員登録トークンはテストに直接関係ない値なので適当な値を生成する
        $uuid = StringUtility::generateUuid();
        $registerToken = hash('sha256', $uuid);

        $sub        = 4;
        $registerId = 4;
        $idSequence = 4;
        $email      = 'email-update-apply-test-fail-banned@gmail.com';

        $registers = [
            'id'             => $sub,
            'register_token' => $registerToken,
            'is_registered'  => 1,
            'expired_on'     => $expiredOn,
            'lock_version'   => 0,
        ];
        \DB::table('registers')->insert($registers);

        $users = [
            'id'          => $sub,
            'register_id' => $registerId,
        ];
        \DB::table('users')->insert($users);

        $names = [
            'id'          => $idSequence,
            'user_id'     => $sub,
            'given_name'  => '百合子',
            'family_name' => '小池',
        ];
        \DB::table('users_names')->insert($names);

        $accounts = [
            'id'          => $sub,
            'register_id' => $registerId,
            'status'      => 2,
        ];
        \DB::table('accounts')->insert($accounts);

        $accountsEmails = [
            'id'         => $idSequence,
            'account_id' => $sub,
            'email'      => $email,
        ];
        \DB::table('accounts_emails')->insert($accountsEmails);

        $passwordValue = ValueFactory::createPasswordValue(
            [
                'password'     => 'password',
                'passwordType' => 0,
            ]
        );

        $accountsPasswords = [
            'account_id'    => $sub,
            'password_hash' => $passwordValue->getPasswordHash(),
            'password_type' => $passwordValue->getPasswordType(),
        ];
        \DB::table('accounts_passwords')->insert($accountsPasswords);
    }

    /**
     * 異常系テスト
     * メールアドレスが既に登録されている
     */
    private function testFailEmailIsAlreadyRegistered()
    {
        $dateTime  = new \DateTime('now');
        $expiredOn = $dateTime->format('Y-m-d H:i:s');

        // 会員登録トークンはテストに直接関係ない値なので適当な値を生成する
        $uuid = StringUtility::generateUuid();
        $registerToken = hash('sha256', $uuid);

        $sub        = 5;
        $registerId = 5;
        $idSequence = 5;
        $email      = 'email-update-apply-test-duplicated@gmail.com';

        $registers = [
            'id'             => $sub,
            'register_token' => $registerToken,
            'is_registered'  => 1,
            'expired_on'     => $expiredOn,
            'lock_version'   => 0,
        ];
        \DB::table('registers')->insert($registers);

        $users = [
            'id'          => $sub,
            'register_id' => $registerId,
        ];
        \DB::table('users')->insert($users);

        $names = [
            'id'          => $idSequence,
            'user_id'     => $sub,
            'given_name'  => '百合子',
            'family_name' => '小池',
        ];
        \DB::table('users_names')->insert($names);

        $accounts = [
            'id'          => $sub,
            'register_id' => $registerId,
        ];
        \DB::table('accounts')->insert($accounts);

        $accountsEmails = [
            'id'         => $idSequence,
            'account_id' => $sub,
            'email'      => $email,
        ];
        \DB::table('accounts_emails')->insert($accountsEmails);

        $passwordValue = ValueFactory::createPasswordValue(
            [
                'password'     => 'password',
                'passwordType' => 0,
            ]
        );

        $accountsPasswords = [
            'account_id'    => $sub,
            'password_hash' => $passwordValue->getPasswordHash(),
            'password_type' => $passwordValue->getPasswordType(),
        ];
        \DB::table('accounts_passwords')->insert($accountsPasswords);
    }

    /**
     * 異常系テスト
     * リクエストされたメールアドレスは過去に強制退会になっている
     */
    private function testFailBannedEmail()
    {
        $dateTime  = new \DateTime('now');
        $expiredOn = $dateTime->format('Y-m-d H:i:s');

        // 会員登録トークンはテストに直接関係ない値なので適当な値を生成する
        $uuid = StringUtility::generateUuid();
        $registerToken = hash('sha256', $uuid);

        $sub         = 6;
        $registerId  = 6;
        $idSequence  = 6;
        $email       = 'email-update-apply-test-fail@gmail.com';
        $bannedEmail = 'email-update-apply-test-banned@gmail.com';

        $registers = [
            'id'             => $sub,
            'register_token' => $registerToken,
            'is_registered'  => 1,
            'expired_on'     => $expiredOn,
            'lock_version'   => 0,
        ];
        \DB::table('registers')->insert($registers);

        $users = [
            'id'          => $sub,
            'register_id' => $registerId,
        ];
        \DB::table('users')->insert($users);

        $names = [
            'id'          => $idSequence,
            'user_id'     => $sub,
            'given_name'  => '百合子',
            'family_name' => '小池',
        ];
        \DB::table('users_names')->insert($names);

        $accounts = [
            'id'          => $sub,
            'register_id' => $registerId,
        ];
        \DB::table('accounts')->insert($accounts);

        $accountsEmails = [
            'id'         => $idSequence,
            'account_id' => $sub,
            'email'      => $email,
        ];
        \DB::table('accounts_emails')->insert($accountsEmails);

        $passwordValue = ValueFactory::createPasswordValue(
            [
                'password'     => 'password',
                'passwordType' => 0,
            ]
        );

        $accountsPasswords = [
            'account_id'    => $sub,
            'password_hash' => $passwordValue->getPasswordHash(),
            'password_type' => $passwordValue->getPasswordType(),
        ];
        \DB::table('accounts_passwords')->insert($accountsPasswords);

        $bannedEmails = [
            'id'         => $idSequence,
            'account_id' => $sub,
            'email'      => $bannedEmail,
        ];
        \DB::table('accounts_banned_emails')->insert($bannedEmails);
    }
}
