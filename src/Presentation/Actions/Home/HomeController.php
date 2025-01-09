<?php

declare(strict_types=1);

namespace App\Presentation\Actions\Home;

use Core\Http\Action;
use Core\Http\Attributes\Route;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use React\Promise\Promise;
use stdClass;

#[Route('GET', '/')]
class HomeController extends Action
{
    private int $counter = 0;

    public function action(Request $request): Response|Promise
    {
        $this->logger->info('HI, person '.$this->counter.PHP_EOL);
        $data = new stdClass;
        $data->message = file_get_contents(__DIR__.'/welcome.txt');

        return $this->respondWithData($data);
    }
}
