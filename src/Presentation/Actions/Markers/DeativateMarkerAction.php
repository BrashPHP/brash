<?php

declare(strict_types=1);

namespace App\Presentation\Actions\Markers;

use App\Domain\Repositories\MarkerRepositoryInterface;
use Core\Http\Action;
use Core\Http\Exceptions\HttpBadRequestException;
use Core\Http\Exceptions\HttpNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DeativateMarkerAction extends Action
{
    public function __construct(
        private MarkerRepositoryInterface $repo
    ) {
    }

    public function action(Request $request): Response
    {
        $id = (int) $this->resolveArg('id');

        if ($id === 0) {
            throw new HttpBadRequestException($request, 'A valid ID should be passed');
        }

        $marker = $this->repo->update($id, ['isActive' => false]);

        if ($marker instanceof \App\Domain\Models\Marker\Marker) {
            return $this->respondWithData($marker);
        }

        throw new HttpNotFoundException($request, 'A marker was not found using the provided id');
    }
}
