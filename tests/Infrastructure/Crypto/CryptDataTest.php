<?php

declare(strict_types=1);
use App\Infrastructure\Cryptography\DataEncryption\Encrypter;

use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertSame;

beforeEach(function () {
    $this->sut = new Encrypter('hashkey');
});
test('if encrypter makes encryption', function () {
    $plaintext = 'Ola, pessoal';
    assertNotSame($plaintext, $this->sut->encrypt($plaintext));
});
test('if encrypter makes decription', function () {
    $plaintext = 'Ola, pessoal';
    $crypted = $this->sut->encrypt($plaintext);

    assertSame($plaintext, $this->sut->decrypt($crypted));
});
