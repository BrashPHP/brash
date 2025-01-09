<?php

declare(strict_types=1);

namespace App\Presentation\Actions\Markers;

use App\Domain\Repositories\MarkerRepositoryInterface;
use Core\Http\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetAllMarkersAction extends Action
{
    public function __construct(
        private MarkerRepositoryInterface $repo,
    ) {}

    public function action(Request $request): Response
    {
        $markers = [];
        $params = $request->getQueryParams();
        if (isset($params['paginate'])) {
            $params['paginate'] = (bool) $params['paginate'];

            $markers = $this->repo->all(...$params);
        } else {
            $markers = $this->repo->all();
        }

        return $this->respondWithData($markers);
    }
}
