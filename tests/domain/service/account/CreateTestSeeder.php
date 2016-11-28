<?php
/**
 * アカウント作成テストデータ投入
 *
 * @author keita-nishimoto
 * @since 2016-11-07
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Tests\Domain\Service\Account;

use Factories\Account\ValueFactory;
use Illuminate\Database\Seeder;

/**
 * Class CreateTestSeeder
 *
 * @category laravel-api-sample
 * @package Tests\Domain\Service\Account
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
        \DB::table('accounts')->truncate();
        \DB::table('accounts_emails')->truncate();
        \DB::table('accounts_passwords')->truncate();

        $this->testFailEmailIsAlreadyRegistered();
    }

    /**
     * 異常系テスト
     * メールアドレスが既に登録されている
     */
    private function testFailEmailIsAlreadyRegistered()
    {
        $sub           = 1;
        $idSequence    = 1;
        $email         = 'account-create-test-duplicated@gmail.com';
        $accountStatus = 0;

        $accounts = [
            'id'     => $sub,
            'status' => $accountStatus,
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
                'password' => '9password1',
            ]
        );

        $accountsPasswords = [
            'account_id'    => $sub,
            'password_hash' => $passwordValue->getPasswordHash(),
        ];
        \DB::table('accounts_passwords')->insert($accountsPasswords);
    }
}
