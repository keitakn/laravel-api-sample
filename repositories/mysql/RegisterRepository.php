<?php
/**
 * 会員登録リポジトリ
 *
 * @author keita-nishimoto
 * @since 2016-09-13
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 * @link https://readouble.com/laravel/5.3/ja/controllers.html
 */

namespace Repositories\Mysql;

use Domain\Register;
use Exceptions\DomainException;
use Factories\Register\EntityFactory;

/**
 * Class RegisterRepository
 *
 * @category laravel-api-sample
 * @package Repositories
 * @author keita-nishimoto
 * @since 2016-09-13
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class RegisterRepository implements Register\RegisterRepositoryInterface
{
    /**
     * 自身のインスタンスを生成する
     *
     * @return \Repositories\Mysql\RegisterRepository
     */
    public static function getInstance()
    {
        $instanceKey = 'RegisterRepository';
        try {
            $instance = \App::make($instanceKey);
            if ($instance instanceof \Repositories\Mysql\RegisterRepository) {
                return $instance;
            }
        } catch (\ReflectionException $e) {
            \App::singleton($instanceKey, '\Repositories\Mysql\RegisterRepository');
            $instance = \App::make($instanceKey);

            return $instance;
        }
    }

    /**
     * 会員登録Entityを保存する
     *
     * @param Register\RegisterEntity $registerEntity
     * @return Register\RegisterEntity
     */
    public function saveRegisterEntity(Register\RegisterEntity $registerEntity)
    {
        $values = [
            'register_token' => $registerEntity->getRegisterToken(),
            'expired_on'     => $registerEntity->getExpiredOn(),
        ];

        $registerId = $this->saveRegister($values);
        $registerEntity->setRegisterId($registerId);

        $values = [
            'register_id' => $registerEntity->getRegisterId(),
            'email'       => $registerEntity->getRegisterTmpValuesEntity()->getEmail(),
        ];

        $tmpValues = $registerEntity->getRegisterTmpValuesEntity()->getTmpValues();
        if (is_null($tmpValues) === false) {
            $values['tmp_values'] = $tmpValues;
        }

        $registerTmpValuesId = $this->saveRegistersTmpValues($values);
        $registerEntity->getRegisterTmpValuesEntity()->setRegisterTmpValuesId($registerTmpValuesId);

        return $registerEntity;
    }

    /**
     * 会員登録Entityを更新する
     *
     * @param Register\RegisterEntity $registerEntity
     * @return Register\RegisterEntity
     * @throws DomainException
     */
    public function updateRegisterEntity(Register\RegisterEntity $registerEntity)
    {
        $nextLockVersion = $registerEntity->getLockVersion() + 1;

        $values = [
            'is_registered' => $registerEntity->isIsRegistered(),
            'lock_version'  => $nextLockVersion,
        ];

        $where = [
            'id'           => $registerEntity->getRegisterId(),
            'lock_version' => $registerEntity->getLockVersion(),
        ];

        $result = \DB::table('registers')->where($where)->update($values);
        if ($result === 0) {
            throw new DomainException(40001);
        }

        $registerEntity->setLockVersion(
            $nextLockVersion
        );

        return $registerEntity;
    }

    /**
     * 会員登録Entityを取得する
     *
     * @param $registerToken
     * @return Register\RegisterEntity
     */
    public function findRegisterEntity($registerToken)
    {
        $selectColumns = [
            'r.id',
            'r.register_token',
            'r.is_registered',
            'r.expired_on',
            'r.lock_version AS registers_lock_version',
            'rv.id AS register_tmp_values_id',
            'rv.email',
            'rv.tmp_values',
        ];

        $registers = \DB::table('registers AS r')
            ->select($selectColumns)
            ->join('registers_tmp_values AS rv', 'r.id', '=', 'rv.register_id')
            ->where('r.register_token', '=', $registerToken)
            ->first();

        if (is_null($registers) === true) {
            return null;
        }

        $registerEntity = EntityFactory::createRegisterEntity($registerToken);
        $registerEntity
            ->setRegisterId($registers->id)
            ->setIsRegistered((int)$registers->is_registered)
            ->setExpiredOn($registers->expired_on)
            ->setLockVersion((int)$registers->registers_lock_version);

        $registerTmpValuesEntity = EntityFactory::createRegisterTmpValuesEntity($registerEntity);
        $registerTmpValuesEntity->setRegisterTmpValuesId($registers->register_tmp_values_id)
            ->setEmail($registers->email)
            ->setTmpValues(
                json_decode($registers->tmp_values, true)
            );

        $registerEntity->setRegisterTmpValuesEntity($registerTmpValuesEntity);

        return $registerEntity;
    }

    /**
     * registersテーブルにデータを保存する
     *
     * @param $params
     * @return string
     * @throws DomainException
     */
    private function saveRegister($params)
    {
        $values = [
            'register_token' => $params['register_token'],
            'expired_on'     => $params['expired_on'],
            'lock_version'   => 0,
        ];

        $result = \DB::table('registers')->insert($values);
        if ($result === false) {
            throw new DomainException(20000);
        }

        return \DB::getPdo()->lastInsertId();
    }

    /**
     * registers_tmp_valuesテーブルにデータを保存する
     *
     * @param $params
     * @return string
     * @throws DomainException
     */
    private function saveRegistersTmpValues($params)
    {
        $values = [
            'register_id'  => $params['register_id'],
            'email'        => $params['email'],
            'lock_version' => 0,
        ];

        if (array_key_exists('tmp_values', $params)) {
            $values['tmp_values'] = json_encode($params['tmp_values']);
        }

        $result = \DB::table('registers_tmp_values')->insert($values);
        if ($result === false) {
            throw new DomainException(20000);
        }

        return \DB::getPdo()->lastInsertId();
    }
}
