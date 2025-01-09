<?php

namespace Tests\Domain\UseCases\Media;

use App\Data\UseCases\Media\MediaCollectorVisitor;

beforeEach(function () {
    $this->sut = new MediaCollectorVisitor;
});

test('should have no elements in visitor array set for empty abstract asset', function () {
    /** @var \Mockery\MockInterface */
    $mhi = mock(MediaHostInterfaceStub::class);
    $mhi->expects('assetInformation')->andReturn(null);
    $this->sut->visit($mhi);

    expect($this->sut->collect())->toBeEmpty();
});

test('should have one element when asset is present', function () {
    /** @var \Mockery\MockInterface */
    $mhi = mock(MediaHostInterfaceStub::class);

    $mhi->expects('namedBy')->andReturn('');

    $mhi->expects('assetInformation')->andReturn(createAbstractAsset());

    $this->sut->visit($mhi);

    expect(count($this->sut->collect()))->toEqual(1);
    expect('path')->toEqual($this->sut->collect()[0]->path);
});

test('should have five elements in collection', function () {
    $arr = [];
    for ($i = 0; $i < 5; $i++) {
        $arr[] = new MediaHostInterfaceStub;
    }

    foreach ($arr as $el) {
        $this->sut->visit($el);
    }

    expect(count($this->sut->collect()))->toEqual(5);
});
