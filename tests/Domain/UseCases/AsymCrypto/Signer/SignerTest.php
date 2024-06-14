<?php

declare(strict_types=1);

namespace Tests\Domain\UseCases\AsymCrypto\Signer;

use App\Data\Protocols\Cryptography\AsymmetricEncrypter;
use App\Data\UseCases\AsymCrypto\AsymmetricSigner;
use App\Domain\Dto\Signature;
use App\Domain\Exceptions\Museum\MuseumNotFoundException;
use App\Domain\Models\Museum;
use App\Domain\Repositories\MuseumRepository;
use App\Domain\Repositories\SignatureTokenRepositoryInterface;
use Ramsey\Uuid\Uuid;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

beforeEach(function () {
    $this->defaultUuid = '5a4bd710-aab8-4ebc-b65d-0c059a960cfb';
    $this->repository = createMuseumRepositoryMock();
    $this->encrypter = createAsymmetricEncrypterMock();
    $this->signatureTokenRepository = createTokenRepositoryMock();

    $this->signer = new AsymmetricSigner($this->repository, $this->encrypter, $this->signatureTokenRepository);
});

it('inserts into signature token repository', function () {
    $uuid = Uuid::fromString($this->defaultUuid);
    $museum = new Museum(id: 2, email: '', name: '', uuid: $uuid);

    $this->encrypter->expects('encrypt')->andReturn(new Signature('privKey', 'pubKey', 'signature'));
    $this->repository->expects('findByUUID')->andReturn($museum);
    $this->signatureTokenRepository->shouldReceive('save')->once()->andReturn(true);

    $this->signer->sign($uuid);
});

it('returns valid 64-based string', function () {
    $uuid = Uuid::fromString($this->defaultUuid);
    $museum = new Museum(id: 2, email: '', name: 'test_museum', uuid: $uuid);
    $subject = json_encode(['uuid' => $this->defaultUuid, 'museum_name' => 'test_museum']);
    $encodedUuid = base64_encode($this->defaultUuid);
    $encodedPrivateKey = base64_encode('pubKey');
    $payload = "{$encodedUuid}.{$encodedPrivateKey}";

    $this->repository->shouldReceive('findByUUID')->andReturn($museum);
    $this->signatureTokenRepository->shouldReceive('save')->once()->andReturn(true);
    $this->encrypter->expects('encrypt')->once()->with($subject)->andReturn(new Signature('privKey', 'pubKey', 'test'));

    $response = $this->signer->sign($uuid);
    list($responseUuid, $responsePrivateKey) = explode('.', $response);

    assertSame(base64_decode($responseUuid, true), $this->defaultUuid);
    assertSame(base64_decode($responsePrivateKey, true), 'pubKey');
    assertSame($payload, $response);
});

it('ensures AsymmetricEncrypter receives correct values', function () {
    $uuid = Uuid::fromString($this->defaultUuid);
    $museum = new Museum(id: 2, email: '', name: 'test_museum', uuid: $uuid);
    $subject = json_encode(['uuid' => $this->defaultUuid, 'museum_name' => 'test_museum']);

    $this->repository->expects('findByUUID')->andReturn($museum);
    $this->signatureTokenRepository->shouldReceive('save')->once()->andReturn(true);
    $this->encrypter->expects('encrypt')->once()->with($subject)->andReturn(new Signature('privKey', 'pubKey', 'signature'));

    $response = $this->signer->sign($uuid);

    assertTrue(is_string($response));
});

it('calls repository with correct values', function () {
    $uuid = Uuid::fromString($this->defaultUuid);
    $museum = new Museum(id: 1, email: '', name: '', uuid: $uuid);

    $this->repository->shouldReceive('findByUUID')->once()->with($this->defaultUuid)->andReturn($museum);
    $this->encrypter->shouldReceive('encrypt')->once()->andReturn(new Signature('privKey', 'pubKey', 'signature'));
    $this->signatureTokenRepository->shouldReceive('save')->once()->andReturn(true);

    $response = $this->signer->sign($uuid);

    assertTrue(is_string($response));
});

it('throws when museum is not found', function () {
    $uuid = Uuid::fromString($this->defaultUuid);
    $this->repository->shouldReceive('findByUUID')->with($this->defaultUuid)->andReturn(null);

    expect(fn() => $this->signer->sign($uuid))->toThrow(MuseumNotFoundException::class);
});

function createTokenRepositoryMock(): SignatureTokenRepositoryInterface
{
    return mock(SignatureTokenRepositoryInterface::class);
}

function createMuseumRepositoryMock(): MuseumRepository
{
    return mock(MuseumRepository::class);
}

function createAsymmetricEncrypterMock(): AsymmetricEncrypter
{
    return mock(AsymmetricEncrypter::class);
}
