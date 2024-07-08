<?php

namespace App\Data\UseCases\Authentication;

use App\Domain\Dto\AccountDto;
use App\Domain\Dto\TokenLoginResponse;
use App\Domain\Exceptions\Account\UserAlreadyRegisteredException;
use App\Domain\Repositories\AccountRepository;
use App\Data\Protocols\Auth\SignUpServiceInterface;
use App\Domain\Factories\TokenResponseFactory;

class SignUp implements SignUpServiceInterface
{
    public function __construct(private AccountRepository $accountRepository)
    {
    }


    public function register(AccountDto $accountDto): TokenLoginResponse
    {
        $account = $this->accountRepository->findWithAuthType($accountDto->email, $accountDto->authType);
        if (is_null($account)) {
            $account = $this->accountRepository->insert($accountDto);

            return TokenResponseFactory::createToken($account);
        }

        throw new UserAlreadyRegisteredException();
    }
}
