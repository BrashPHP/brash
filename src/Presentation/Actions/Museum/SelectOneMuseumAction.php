<?php

declare(strict_types=1);

namespace App\Presentation\Actions\Museum;

use App\Domain\Repositories\MuseumRepository;
use Core\Http\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SelectOneMuseumAction extends Action
{
    public function __construct(
        private MuseumRepository $museumRepository
    ) {}

    public function action(Request $request): Response
    {
        $id = (int) $this->resolveArg('id');
        $museum = $this->museumRepository->findByID($id);

        return $this->respondWithData(['museum' => $museum]);
    }
}
