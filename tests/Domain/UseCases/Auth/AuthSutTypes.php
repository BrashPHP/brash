<?php
namespace Tests\Domain\UseCases\Auth;

use App\Data\Protocols\Auth\LoginServiceInterface;
use App\Data\UseCases\Authentication\Login;

final class AuthSutTypes
{
    public LoginServiceInterface $service;


    public function __construct(
        public $repository,
        public $comparer
    ) {
        $this->service = new Login($repository, $comparer);
    }
}
