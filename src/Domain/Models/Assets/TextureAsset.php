<?php

namespace App\Domain\Models\Assets;

class TextureAsset extends AbstractAsset
{
    public function __construct()
    {
        parent::__construct('texture');
    }
}
