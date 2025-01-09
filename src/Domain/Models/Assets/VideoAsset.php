<?php

namespace App\Domain\Models\Assets;

class VideoAsset extends AbstractAsset
{
    public function __construct()
    {
        parent::__construct('video');
    }
}
