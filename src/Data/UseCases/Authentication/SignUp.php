<?php

namespace App\Data\UseCases\Authentication;

use App\Data\Protocols\Auth\SignUpServiceInterface;
use App\Domain\Dto\AccountDto;
use App\Domain\Dto\TokenLoginResponse;
use App\Domain\Exceptions\Account\UserAlreadyRegisteredException;
use App\Domain\Factories\TokenResponseFactory;
use App\Domain\Repositories\AccountRepository;

class SignUp implements SignUpServiceInterface
{
    public function __construct(private AccountRepository $accountRepository) {}

    public function register(AccountDto $accountDto): TokenLoginResponse
    {
        $account = $this->accountRepository->findWithAuthType($accountDto->email, $accountDto->authType);
        if (is_null($account)) {
            $account = $this->accountRepository->insert($accountDto);

            return TokenResponseFactory::createToken($account);
        }

        throw new UserAlreadyRegisteredException;
    }
}
