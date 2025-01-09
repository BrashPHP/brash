<?php

namespace App\Domain\Models\Assets;

class PictureAsset extends AbstractAsset
{
    public function __construct()
    {
        parent::__construct('picture');
    }
}
