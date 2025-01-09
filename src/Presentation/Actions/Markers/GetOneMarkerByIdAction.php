<?php

declare(strict_types=1);

namespace App\Presentation\Actions\Markers;

use App\Domain\Repositories\MarkerRepositoryInterface;
use App\Presentation\Actions\Markers\Utils\PresignedUrlCreator;
use Core\Http\Action;
use Core\Http\Exceptions\HttpBadRequestException;
use Core\Http\Exceptions\HttpNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetOneMarkerByIdAction extends Action
{
    public function __construct(
        private MarkerRepositoryInterface $repo,
        private PresignedUrlCreator $presignedUrlCreator
    ) {}

    public function action(Request $request): Response
    {
        $id = (int) $this->resolveArg('id');

        if ($id === 0) {
            throw new HttpBadRequestException($request, 'A valid ID should be passed');
        }

        $marker = $this->repo->findByID($id);

        if (! $marker instanceof \App\Domain\Models\Marker\Marker) {
            throw new HttpNotFoundException($request, 'No marker found using this id');
        }

        if (($asset = $marker->assetInformation()) instanceof \App\Domain\Models\Assets\AbstractAsset) {
            $asset->setTemporaryLocation($this->presignedUrlCreator->setPresignedUrl($asset));
        }

        foreach ($marker->resources as $res) {
            if ($asset = $res->assetInformation()) {
                $asset->setTemporaryLocation($this->presignedUrlCreator->setPresignedUrl($asset));
            }
        }

        return $this->respondWithData($marker);
    }
}
