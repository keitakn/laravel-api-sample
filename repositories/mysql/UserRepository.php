<?php
/**
 * ユーザーリポジトリ
 *
 * @author keita-nishimoto
 * @since 2016-09-20
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 * @link https://readouble.com/laravel/5.3/ja/controllers.html
 */

namespace Repositories\Mysql;

use Domain\Register\RegisterEntity;
use Domain\User;
use Domain\User\UserEntity;
use Factories\User\EntityFactory;
use Factories\User\ValueFactory;
use Exceptions\DomainException;

/**
 * Class UserRepository
 *
 * @category laravel-api-sample
 * @package Repositories
 * @author keita-nishimoto
 * @since 2016-09-20
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class UserRepository implements User\UserRepositoryInterface
{

    /**
     * 自身のインスタンスを生成する
     *
     * @return \Repositories\Mysql\UserRepository
     */
    public static function getInstance()
    {
        $instanceKey = 'UserRepository';
        try {
            $instance = \App::make($instanceKey);
            if ($instance instanceof \Repositories\Mysql\UserRepository) {
                return $instance;
            }
        } catch (\ReflectionException $e) {
            \App::singleton($instanceKey, '\Repositories\Mysql\UserRepository');
            $instance = \App::make($instanceKey);

            return $instance;
        }
    }

    /**
     * ユーザーEntityを新規で作成する
     *
     * @param RegisterEntity $registerEntity
     * @param array $params
     * @return UserEntity
     */
    public function createUserEntity(RegisterEntity $registerEntity, $params = [])
    {
        $userParams = [
            'register_id' => $registerEntity->getRegisterId(),
        ];

        if (array_key_exists('gender', $params) === true) {
            $userParams['gender'] = $params['gender'];
        }

        if (array_key_exists('birthdate', $params) === true) {
            $userParams['birthdate'] = $params['birthdate'];
        }

        $sub = $this->saveUsers($userParams);

        $userEntity = EntityFactory::createUserEntity($sub);
        $userEntity->setRegisterId($registerEntity->getRegisterId())
            ->setLockVersion(0);

        if (array_key_exists('gender', $params) === true) {
            $userEntity->setGender($params['gender']);
        }

        if (array_key_exists('birthdate', $params) === true) {
            $userEntity->setBirthdate($params['birthdate']);
        }

        return $userEntity;
    }

    /**
     * ユーザーEntityを取得する
     *
     * @param $sub
     * @return UserEntity
     */
    public function findUserEntity($sub)
    {
        $selectColumns = [
            'u.id AS sub',
            'u.register_id',
            'u.gender',
            'u.birthdate',
            'u.lock_version AS users_lock_version',
            'na.given_name',
            'na.family_name',
            'na.preferred_username',
            'na.given_name_kana',
            'na.family_name_kana',
            'na.id AS name_id',
            'na.lock_version AS name_lock_version',
            'oc.occupation_code',
            'oc.id AS occupation_id',
            'oc.lock_version AS occupation_lock_version',
        ];

        $users = \DB::table('users AS u')
            ->select($selectColumns)
            ->join('users_names AS na', 'u.id', '=', 'na.user_id')
            ->leftJoin('users_occupations AS oc', 'u.id', '=', 'oc.user_id')
            ->where('u.id', '=', $sub)
            ->first();

        if (is_null($users) === true) {
            return $users;
        }

        $userEntity = $this->newUserEntityFromUserData($users);

        $addressValues = $this->findUsersAddresses($userEntity);
        if (is_null($addressValues) === false) {
            $userEntity->setAddressValues($addressValues);
        }

        $schoolValues = $this->findUsersSchools($userEntity);
        if (is_null($schoolValues) === false) {
            $userEntity->setSchoolValues($schoolValues);
        }

        return $userEntity;
    }

    /**
     * 名前オブジェクトを保存する
     *
     * @param UserEntity $userEntity
     * @return UserEntity
     * @throws DomainException
     */
    public function saveName(UserEntity $userEntity)
    {
        $nameValue = $userEntity->getNameValue();

        $values = [
            'user_id'            => $userEntity->getSub(),
            'given_name'         => $nameValue->getGivenName(),
            'family_name'        => $nameValue->getFamilyName(),
            'middle_name'        => null,
            'nickname'           => null,
            'preferred_username' => $nameValue->getPreferredUsername(),
            'given_name_kana'    => $nameValue->getGivenNameKana(),
            'family_name_kana'   => $nameValue->getFamilyNameKana(),
        ];

        $result = \DB::table('users_names')->insert($values);
        if ($result === false) {
            throw new DomainException(20000);
        }

        $nameId = \DB::getPdo()->lastInsertId();

        $newNameValue = ValueFactory::createNameValue(
            [
                'givenName'         => $nameValue->getGivenName(),
                'familyName'        => $nameValue->getFamilyName(),
                'preferredUsername' => $nameValue->getPreferredUsername(),
                'givenNameKana'     => $nameValue->getGivenNameKana(),
                'familyNameKana'    => $nameValue->getFamilyNameKana(),
                'lockVersion'       => $nameValue->getLockVersion(),
                'id'                => (int)$nameId,
            ]
        );

        $userEntity->setNameValue($newNameValue);

        return $userEntity;
    }

    /**
     * 住所オブジェクトを保存する（現住所）
     *
     * @param UserEntity $userEntity
     * @return UserEntity
     * @throws DomainException
     */
    public function saveCurrentAddress(UserEntity $userEntity)
    {
        $addressValue = $userEntity->getCurrentAddress();

        $values = [
            'user_id'        => $userEntity->getSub(),
            'address_type'   => $addressValue->getAddressType(),
            'country'        => $addressValue->getCountry(),
            'postal_code'    => $addressValue->getPostalCode(),
            'region'         => $addressValue->getRegion(),
            'locality'       => $addressValue->getLocality(),
            'street_address' => $addressValue->getStreetAddress(),
            'building'       => $addressValue->getBuilding(),
        ];

        $result = \DB::table('users_addresses')->insert($values);
        if ($result === false) {
            throw new DomainException(20000);
        }

        $addressId = \DB::getPdo()->lastInsertId();

        $newAddressValue = ValueFactory::createAddressValue(
            [
                'addressType'   => $addressValue->getAddressType(),
                'country'       => $addressValue->getCountry(),
                'postalCode'    => $addressValue->getPostalCode(),
                'region'        => $addressValue->getRegion(),
                'locality'      => $addressValue->getLocality(),
                'streetAddress' => $addressValue->getStreetAddress(),
                'building'      => $addressValue->getBuilding(),
                'lockVersion'   => 0,
                'id'            => (int)$addressId,
            ]
        );

        $userEntity->setCurrentAddress($newAddressValue);

        return $userEntity;
    }

    /**
     * 職業オブジェクトを保存する
     *
     * @param UserEntity $userEntity
     * @return UserEntity
     * @throws DomainException
     */
    public function saveOccupation(UserEntity $userEntity)
    {
        $values = [
            'user_id'         => $userEntity->getSub(),
            'occupation_code' => $userEntity->getOccupationValue()->getOccupationCode(),
        ];

        $result = \DB::table('users_occupations')->insert($values);
        if ($result === false) {
            throw new DomainException(20000);
        }

        $occupationId = \DB::getPdo()->lastInsertId();

        $newOccupationValue = ValueFactory::createOccupationValue(
            [
                'occupationCode' => $userEntity->getOccupationValue()->getOccupationCode(),
                'lockVersion'    => 0,
                'id'             => (int)$occupationId,
            ]
        );

        $userEntity->setOccupationValue($newOccupationValue);

        return $userEntity;
    }

    /**
     * 学校オブジェクトを保存する
     *
     * @param UserEntity $userEntity
     * @return UserEntity
     * @throws DomainException
     */
    public function saveSchools(UserEntity $userEntity)
    {
        $schoolValues = $userEntity->getSchoolValues();

        $newSchoolValues = [];
        foreach ($schoolValues as $schoolValue) {
            $values = [
                'user_id'         => $userEntity->getSub(),
                'graduation_year' => $schoolValue->getGraduationYear(),
            ];

            $result = \DB::table('users_schools')->insert($values);
            if ($result === false) {
                throw new DomainException(20000);
            }

            $schoolId = \DB::getPdo()->lastInsertId();
            $newSchoolValue = ValueFactory::createSchoolValue(
                [
                    'graduationYear' => $schoolValue->getGraduationYear(),
                    'id'             => (int)$schoolId,
                ]
            );

            $newSchoolValues[] = $newSchoolValue;
        }

        $userEntity->setSchoolValues($newSchoolValues);

        return $userEntity;
    }

    /**
     * usersテーブルにデータを保存する
     *
     * @param $params
     * @return string
     * @throws DomainException
     */
    private function saveUsers($params)
    {
        $values = [
            'register_id'  => $params['register_id'],
            'lock_version' => 0,
        ];

        if (array_key_exists('gender', $params) === true) {
            $values['gender'] = $params['gender'];
        }

        if (array_key_exists('birthdate', $params) === true) {
            $values['birthdate'] = $params['birthdate'];
        }

        $result = \DB::table('users')->insert($values);
        if ($result === false) {
            throw new DomainException(20000);
        }

        return \DB::getPdo()->lastInsertId();
    }

    /**
     * ユーザーデータを元にユーザーEntityを作成する
     *
     * @param \stdClass $users
     * @return UserEntity
     */
    private function newUserEntityFromUserData(\stdClass $users)
    {
        $userEntity = EntityFactory::createUserEntity($users->sub);

        $userEntity->setRegisterId($users->register_id)
            ->setGender($users->gender)
            ->setBirthdate($users->birthdate)
            ->setLockVersion($users->users_lock_version);

        if (property_exists($users, 'given_name') === true) {
            $nameParams = [
                'givenName'   => $users->given_name,
                'familyName'  => $users->family_name,
                'lockVersion' => $users->name_lock_version,
                'id'          => $users->name_id,
            ];

            if (property_exists($users, 'preferred_username') === true) {
                $nameParams['preferredUsername'] = $users->preferred_username;
            }

            if (property_exists($users, 'given_name_kana') === true) {
                $nameParams['givenNameKana'] = $users->given_name_kana;
            }

            if (property_exists($users, 'family_name_kana') === true) {
                $nameParams['familyNameKana'] = $users->family_name_kana;
            }

            $nameValue = ValueFactory::createNameValue(
                $nameParams
            );

            $userEntity->setNameValue($nameValue);
        }

        if (property_exists($users, 'occupation_code') === true) {
            $occupationValue = ValueFactory::createOccupationValue(
                [
                    'occupationCode' => $users->occupation_code,
                    'lockVersion'    => $users->occupation_lock_version,
                    'id'             => $users->occupation_id,
                ]
            );

            $userEntity->setOccupationValue($occupationValue);
        }

        return $userEntity;
    }

    /**
     * DBからユーザーの住所を全て取得する
     *
     * @param $userEntity
     * @return User\AddressValue
     */
    private function findUsersAddresses(UserEntity $userEntity)
    {
        $selectColumns = [
            'id',
            'user_id',
            'address_type',
            'country',
            'postal_code',
            'region',
            'locality',
            'street_address',
            'building',
            'lock_version',
        ];

        $addresses = \DB::table('users_addresses')
            ->select($selectColumns)
            ->where('user_id', '=', $userEntity->getSub())
            ->get();

        if ($addresses->isEmpty() === true) {
            return null;
        }

        $addressValues = $addresses->map(function ($address) {
            $createParams = [
                'addressType' => $address->address_type,
                'country'     => $address->country,
                'region'      => $address->region,
                'lockVersion' => $address->lock_version,
                'id'          => $address->id,
            ];

            if (property_exists($address, 'postal_code') === true) {
                $createParams['postalCode'] = $address->postal_code;
            }

            if (property_exists($address, 'locality') === true) {
                $createParams['locality'] = $address->locality;
            }

            if (property_exists($address, 'street_address') === true) {
                $createParams['streetAddress'] = $address->street_address;
            }

            if (property_exists($address, 'building') === true) {
                $createParams['building'] = $address->building;
            }

            $addressValue = ValueFactory::createAddressValue(
                $createParams
            );

            return $addressValue;
        });

        return $addressValues->all();
    }

    /**
     * DBからユーザーの学校を全て取得する
     *
     * @param UserEntity $userEntity
     * @return array|null
     */
    private function findUsersSchools(UserEntity $userEntity)
    {
        $selectColumns = [
            'id',
            'user_id',
            'graduation_year',
            'school_code',
            'school_name_code',
            'school_name',
            'school_type',
            'lock_version',
        ];

        $schools = \DB::table('users_schools')
            ->select($selectColumns)
            ->where('user_id', '=', $userEntity->getSub())
            ->orderBy('graduation_year', 'desc')
            ->get();

        if ($schools->isEmpty() === true) {
            return null;
        }

        $schoolValues = $schools->map(function ($school) {
            $createParams = [
                'graduationYear' => $school->graduation_year,
                'lockVersion'    => $school->lock_version,
                'id'             => $school->id,
            ];

            if (property_exists($school, 'school_code') === true) {
                $createParams['schoolCode'] = $school->school_code;
            }

            if (property_exists($school, 'school_name_code') === true) {
                $createParams['schoolNameCode'] = $school->school_name_code;
            }

            if (property_exists($school, 'school_name') === true) {
                $createParams['schoolName'] = $school->school_name;
            }

            if (property_exists($school, 'school_type') === true) {
                $createParams['schoolType'] = $school->school_type;
            }

            $schoolValue = ValueFactory::createSchoolValue(
                $createParams
            );

            return $schoolValue;
        });

        return $schoolValues->all();
    }
}
