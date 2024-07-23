<?php

declare(strict_types=1);

namespace App\Application\Providers;

use App\Data\Protocols\AsymCrypto\AsymmetricVerifier;
use App\Data\Protocols\Cryptography\AsymmetricEncrypter;
use App\Data\Protocols\Cryptography\ComparerInterface;
use App\Data\Protocols\Cryptography\DataDecrypter;
use App\Data\Protocols\Cryptography\DataEncrypter;
use App\Data\Protocols\Cryptography\HasherInterface;
use App\Infrastructure\Cryptography\AsymmetricKeyGeneration\AsymmetricOpenSSLVerifier;
use App\Infrastructure\Cryptography\AsymmetricKeyGeneration\OpenSSLAsymmetricEncrypter;
use App\Infrastructure\Cryptography\DataEncryption\Encrypter;
use App\Infrastructure\Cryptography\HashComparer;
use App\Infrastructure\Cryptography\HashCreator;
use League\OAuth2\Client\Provider\Google;

use Core\Providers\AppProviderInterface;

use DI\ContainerBuilder;

class DependenciesProvider implements AppProviderInterface
{
    public function provide(ContainerBuilder $container)
    {
        $container->addDefinitions($this->createDefinitions());
    }

    private function createDefinitions(): array
    {
        /*
         * Sets infrastructure dependencies
         *
         * @param ContainerBuilder $containerBuilder
         */
        $encrypter = new Encrypter($_SERVER['ENCRYPTION_KEY'] ?? '');

        return [
            ComparerInterface::class => new HashComparer(),
            HasherInterface::class => new HashCreator(),
            DataDecrypter::class => $encrypter,
            DataEncrypter::class => $encrypter,
            AsymmetricEncrypter::class => new OpenSSLAsymmetricEncrypter(),
            AsymmetricVerifier::class => new AsymmetricOpenSSLVerifier(),
            
            Google::class => static function (): Google {
                $clientId = $_ENV['GOOGLE_CLIENT_ID'];
                $clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'];
                $redirectUri = $_ENV['GOOGLE_REDIRECT_URI'];

                return new Google(compact('clientId', 'clientSecret', 'redirectUri'));
            }
        ];
    }
}
