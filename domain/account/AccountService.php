<?php
/**
 * アカウントを扱うサービスクラス
 *
 * @author   keita-nishimoto
 * @since    2016-09-09
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Domain\Account;

use Domain\RequestEntity;
use Domain\ResponseEntity;
use Exceptions\DomainException;
use Factories\Account\ValueFactory;
use Factories\EntityFactory;
use Repositories\Mysql\AccountRepository;

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
     * アカウント作成
     *
     * @param RequestEntity $requestEntity
     * @return \Domain\ResponseEntity
     * @throws DomainException
     */
    public function create(RequestEntity $requestEntity): ResponseEntity
    {
        $requestParams = $requestEntity->getRequestParams();

        $sub   = $requestParams['sub'];
        $email = $requestParams['email'];

        $accountRepository = AccountRepository::getInstance();
        $accountEntity = $accountRepository->findAccountEntity($sub);

        $emailValue = ValueFactory::createEmailValue(
            [
                'email'         => $email,
                'emailVerified' => 1,
            ]
        );

        $locationFormat = '%s/v1/accounts/%s';
        $location = sprintf(
            $locationFormat,
            env('APP_URL'),
            $sub
        );

        $header = [
            'Location' => $location,
        ];

        $embedded = [
            'email'          => $emailValue->getEmail(),
            'email_verified' => $emailValue->getEmailVerified(),
            'password_hash'  => $accountEntity->getPasswordValue()->getPasswordHash(),
        ];

        $response = [
            '_links' => [
                'self' => [
                    'href' => "/v1/accounts/$sub"
                ],
            ],
            'sub'            => $sub,
            'account_status' => $accountEntity->convertAccountStatusToString(),
            '_embedded'      => $embedded,
        ];

        $responseEntity = EntityFactory::createResponseEntity($requestEntity);

        $responseEntity->setApiResponse($response)
            ->setHttpStatusCode(201)
            ->setOptionalHeader($header)
            ->createSuccessResponse();

        return $responseEntity;
    }
}
