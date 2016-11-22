<?php
/**
 * アカウントを扱うサービスクラス
 *
 * @author   keita-nishimoto
 * @since    2016-09-09
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Domain\Account;

use Domain\Register\RegisterSpecification;
use Domain\RequestEntity;
use Exceptions\DomainException;
use Factories\Account\ValueFactory;
use Factories\EntityFactory;
use Repositories\Mysql\AccountRepository;
use Repositories\Mysql\UserRepository;

/**
 * Class AccountService
 *
 * @category laravel-api-sample
 * @package Domain\Account
 * @author keita-nishimoto
 * @since 2016-09-09
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class AccountService
{

    /**
     * メールアドレス変更申請
     *
     * @param RequestEntity $requestEntity
     * @return \Domain\ResponseEntity
     * @throws DomainException
     */
    public function emailUpdateApply(RequestEntity $requestEntity)
    {
        $requestParams = $requestEntity->getRequestParams();

        $sub   = $requestParams['sub'];
        $email = $requestParams['email'];

        $accountRepository = AccountRepository::getInstance();
        $accountEntity = $accountRepository->findAccountEntity($sub);
        if (is_null($accountEntity) === true) {
            throw new DomainException(40004);
        }

        if ($accountEntity->isCanceled() === true) {
            throw new DomainException(40008);
        }

        if ($accountEntity->isBanned() === true) {
            throw new DomainException(40009);
        }

        $emailValue = ValueFactory::createEmailValue(
            [
                'email'         => $email,
                'emailVerified' => 1,
            ]
        );

        $isBannedEmail = RegisterSpecification::isBannedEmail($emailValue);
        if ($isBannedEmail === true) {
            throw new DomainException(40006);
        }

        $registerable = RegisterSpecification::canRegisterableEmail($emailValue);
        if ($registerable === false) {
            throw new DomainException(40000);
        }

        $emailVerifyTokenEntity = AccountSpecification::newEmailVerifyTokenEntity($emailValue, $sub);
        $emailVerifyTokenEntity->save();

        $emailVerifyToken = $emailVerifyTokenEntity->getEmailVerifyToken();

        $locationFormat = '%s/v1/accounts/%s/emails/%s';
        $location = sprintf(
            $locationFormat,
            env('APP_URL'),
            $sub,
            $emailVerifyToken
        );

        $header = [
            'Location' => $location,
        ];

        $response = [
            '_links' => [
                'self' => [
                    'href' => "/v1/accounts/$sub/emails/$emailVerifyToken"
                ],
            ],
            'email_verify_token' => $emailVerifyToken,
            'expired_on'         => $emailVerifyTokenEntity->getExpiredOn(),
        ];

        $responseEntity = EntityFactory::createResponseEntity($requestEntity);

        $responseEntity->setApiResponse($response)
            ->setHttpStatusCode(201)
            ->setOptionalHeader($header)
            ->createSuccessResponse();

        return $responseEntity;
    }

    /**
     * メールアドレス変更完了
     *
     * @param RequestEntity $requestEntity
     * @return \Domain\ResponseEntity
     * @throws DomainException
     */
    public function emailUpdateComplete(RequestEntity $requestEntity)
    {
        $requestParams = $requestEntity->getRequestParams();

        $sub              = $requestParams['sub'];
        $emailVerifyToken = $requestParams['email_verify_token'];

        $accountRepository = AccountRepository::getInstance();
        $emailVerifyTokenEntity = $accountRepository->findEmailVerifyTokenEntity($emailVerifyToken);
        if (is_null($emailVerifyTokenEntity) === true) {
            throw new DomainException(40011);
        }

        if ($emailVerifyTokenEntity->IsVerified() === true) {
            throw new DomainException(40010);
        }

        $dateTime = new \DateTime('now');
        $nowDateTime = $dateTime->format('Y-m-d H:i:s');
        if ($nowDateTime >= $emailVerifyTokenEntity->getExpiredOn()) {
            throw new DomainException(40012);
        }

        $accountEntity = $accountRepository->findAccountEntity($sub);
        if (is_null($accountEntity) === true) {
            throw new DomainException(40004);
        }

        if (AccountSpecification::subMatches($accountEntity, $emailVerifyTokenEntity) === false) {
            throw new DomainException(40013);
        }

        if ($accountEntity->isCanceled() === true) {
            throw new DomainException(40008);
        }

        if ($accountEntity->isBanned() === true) {
            throw new DomainException(40009);
        }

        $isBannedEmail = RegisterSpecification::isBannedEmail(
            $emailVerifyTokenEntity->getEmailValue()
        );
        if ($isBannedEmail === true) {
            throw new DomainException(40006);
        }

        $registerable = RegisterSpecification::canRegisterableEmail(
            $emailVerifyTokenEntity->getEmailValue()
        );
        if ($registerable === false) {
            throw new DomainException(40000);
        }

        $accountEntity->updateEmail(
            $emailVerifyTokenEntity->getEmailValue()
        );

        $emailVerifyTokenEntity->setIsVerified(1)->update();

        $response = [
            '_links' => [
                'self' => [
                    'href' => "/v1/users/$sub"
                ],
            ],
            'email'          => $accountEntity->getEmailValue()->getEmail(),
            'email_verified' => $accountEntity->getEmailValue()->isEmailVerified(),
        ];

        $responseEntity = EntityFactory::createResponseEntity($requestEntity);

        $responseEntity->setApiResponse($response)
            ->setHttpStatusCode(201)
            ->createSuccessResponse();

        return $responseEntity;
    }
}
