<?php

namespace Tests\Domain\UseCases\AsymCrypto\Signer;

use App\Data\Protocols\Cryptography\AsymmetricEncrypter;
use App\Domain\Dto\Signature;
use App\Domain\Models\Museum;
use Ramsey\Uuid\Uuid;

trait AsymmetricEncrypterTestTrait
{
    public function testIfAsymmetricEncrypterReceivesValues()
    {
        $encrypter = $this->sut->encrypter;

        $repo = $this->sut->repository;
        $uuid = Uuid::fromString($this->defaultUuid);
        $museum = new Museum(2, email: '', name: 'test_museum', uuid: $uuid);
        $repo->expects('findByUUID')->andReturn($museum);

        $subject = json_encode(
            [
                'uuid' => $this->defaultUuid,
                'museum_name' => 'test_museum',
            ]
        );

        $encrypter->expects('encrypt')->once()->with($subject)->andReturn(new Signature('privKey', 'pubKey', 'signature'));

        $response = $this->sut->signer->sign($uuid);

        $this->assertTrue(is_string($response));
    }

}
