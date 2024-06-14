<?php

declare(strict_types=1);
use App\Data\Protocols\Cryptography\AsymmetricEncrypter;
use App\Data\UseCases\AsymCrypto\AsymmetricSigner;
use App\Domain\Dto\Signature;
use App\Domain\Models\Museum;
use App\Domain\Repositories\MuseumRepository;
use App\Domain\Repositories\SignatureTokenRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use Tests\Domain\UseCases\AsymCrypto\Signer\SignerSutTypes as SutTypes;
uses(\Tests\Domain\UseCases\AsymCrypto\Signer\RepositoryBatteryTestTrait::class);

uses(\Tests\Domain\UseCases\AsymCrypto\Signer\AsymmetricEncrypterTestTrait::class);

beforeEach(function () {
    /** @var MuseumRepository */
    $repository = $this->createMockRepository();

    /** @var AsymmetricEncrypter */
    $encrypter = $this->createEncrypterMock();

    /** @var SignatureTokenRepositoryInterface */
    $signatureTokenRepository = createTokenRepositoryMock();

    $signer = new AsymmetricSigner($repository, $encrypter, $signatureTokenRepository);

    $this->sut = new SutTypes($signer, $repository, $encrypter, $signatureTokenRepository);
});
test('if signature token repository makes insertion', function () {
    /** @var MockObject */
    $tokenRepository = $this->sut->signatureTokenRepository;

    /** @var MockObject */
    $encrypter = $this->sut->encrypter;

    /** @var MockObject */
    $museumRepository = $this->sut->repository;

    $encrypter->method('encrypt')->willReturn(new Signature('privKey', 'pubKey', 'signature'));

    $uuid = Uuid::fromString('5a4bd710-aab8-4ebc-b65d-0c059a960cfb');

    $museum = new Museum(id: 2, email: '', name: '', uuid: $uuid);

    $museumRepository->method('findByUUID')->willReturn($museum);

    $tokenRepository->expects($this->once())->method('save');

    $uuid = Uuid::fromString('5a4bd710-aab8-4ebc-b65d-0c059a960cfb');

    $this->sut->signer->sign($uuid);
});
/**
 * Create a mocked login service.
 *
 * @return MockObject
 */
function createTokenRepositoryMock()
{
    return $this->getMockBuilder(SignatureTokenRepositoryInterface::class)
        ->onlyMethods(['save'])
        ->disableOriginalConstructor()
        ->getMock();
}
