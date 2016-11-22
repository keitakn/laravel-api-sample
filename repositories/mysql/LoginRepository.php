<?php
/**
 * ログインリポジトリ
 *
 * @author keita-nishimoto
 * @since 2016-10-20
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 * @link https://readouble.com/laravel/5.3/ja/controllers.html
 */

namespace Repositories\Mysql;

use Domain\Login\AuthenticationEntity;
use Domain\Login\LoginRepositoryInterface;
use Domain\Login\LoginSessionEntity;
use Exceptions\DomainException;

/**
 * Class LoginRepository
 *
 * @category laravel-api-sample
 * @package Repositories\Mysql
 * @author keita-nishimoto
 * @since 2016-10-20
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class LoginRepository implements LoginRepositoryInterface
{
    /**
     * 自身のインスタンスを生成する
     *
     * @return $this
     */
    public static function getInstance()
    {
        $instanceKey = 'LoginRepository';
        try {
            $instance = \App::make($instanceKey);
            if ($instance instanceof \Repositories\Mysql\LoginRepository) {
                return $instance;
            }
        } catch (\ReflectionException $e) {
            \App::singleton($instanceKey, '\Repositories\Mysql\LoginRepository');
            $instance = \App::make($instanceKey);

            return $instance;
        }
    }

    /**
     * ログインセッションEntityを保存する
     *
     * @param LoginSessionEntity $loginSessionEntity
     * @return LoginSessionEntity
     * @throws DomainException
     */
    public function saveLoginSessionEntity(LoginSessionEntity $loginSessionEntity)
    {
        $values = [
            'login_session_token' => $loginSessionEntity->getLoginSessionToken(),
            'sub'                 => $loginSessionEntity->getSub(),
            'expired_on'          => $loginSessionEntity->getExpiredOn(),
        ];

        $result = \DB::table('login_sessions')->insert($values);
        if ($result === false) {
            throw new DomainException(20000);
        }

        $loginSessionId = \DB::getPdo()->lastInsertId();
        $loginSessionEntity->setLoginSessionId((int)$loginSessionId);

        return $loginSessionEntity;
    }

    /**
     * 認証Entityを保存する
     *
     * @param AuthenticationEntity $authenticationEntity
     * @return AuthenticationEntity
     */
    public function saveAuthenticationEntity(AuthenticationEntity $authenticationEntity)
    {
        $values = [
            'authentication_token' => $authenticationEntity->getAuthenticationToken(),
            'sub'                  => $authenticationEntity->getSub(),
            'expired_on'           => $authenticationEntity->getExpiredOn(),
        ];

        $authenticationId = $this->saveAuthentications($values);

        $authenticationEntity->setAuthenticationId($authenticationId);

        return $authenticationEntity;
    }

    /**
     * authenticationsテーブルにデータを保存する
     *
     * @param array $values
     * @return int
     * @throws DomainException
     */
    private function saveAuthentications($values = [])
    {
        $result = \DB::table('authentications')->insert($values);
        if ($result === false) {
            throw new DomainException(20000);
        }

        $authenticationId = \DB::getPdo()->lastInsertId();

        return (int)$authenticationId;
    }
}
