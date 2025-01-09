<?php

namespace Tests\Domain\UseCases\Media;

use App\Data\Protocols\Media\MediaCollectorInterface;
use App\Data\Protocols\Media\MediaHostInterface;
use App\Domain\Models\Assets\AbstractAsset;

function createAbstractAsset(): AbstractAsset
{
    return new class extends AbstractAsset
    {
        public function __construct(public string $mediaType = 'stub') {}

        public function getPath(): string
        {
            return 'path';
        }
    };
}

class MediaHostInterfaceStub implements MediaHostInterface
{
    public function assetInformation(): ?AbstractAsset
    {
        return createAbstractAsset();
    }

    public function accept(MediaCollectorInterface $visitor): void
    {
        $visitor->visit($this);
    }

    public function namedBy(): string
    {
        return 'named';
    }

    public function jsonSerialize(): mixed
    {
        return [];
    }
}
