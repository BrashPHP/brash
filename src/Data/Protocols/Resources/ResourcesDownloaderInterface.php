<?php

namespace App\Data\Protocols\Resources;

use App\Domain\Exceptions\Museum\MuseumNotFoundException;

interface ResourcesDownloaderInterface
{
    /**
     * Returns all mapped marker instances from a museum
     *
     *
     * @param  string  $uuid
     * @return \App\Domain\Dto\Asset\Transference\MarkerResource[]
     *
     * @throws MuseumNotFoundException
     */
    public function transport(int $id): array;
}
