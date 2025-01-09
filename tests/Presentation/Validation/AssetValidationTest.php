<?php

namespace Tests\Presentation\Validation;

use App\Presentation\Actions\Markers\MarkerValidations\AssetValidation;

test('should fail for empty asset', function () {
    $this->sut = new AssetValidation;
    $asset = [];
    expect($this->sut->validation()->validate($asset))->toBeFalse();
});
