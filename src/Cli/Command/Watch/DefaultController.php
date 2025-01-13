<?php

namespace Brash\Framework\Cli\Command\Watch;

use Brash\Framework\Cli\Command\BaseController;
use Brash\Framework\Cli\Services\FindProjectRootService;
use Brash\Framework\Cli\Services\ProcessService;
use Brash\Framework\Cli\Views\Cli\ErrorMessage;
use Brash\PhpWatcher\Bootstrapper;
use Brash\PhpWatcher\EffectEventWatchEnum;
use Brash\PhpWatcher\PathTypeEnum;
use Brash\PhpWatcher\Watcher;
use Brash\PhpWatcher\WatchEvent;
use Revolt\EventLoop;


class DefaultController extends BaseController
{
    public function handle(): void
    {
        $path = $this->hasParam('path') ? $this->getParam('path') : '';
        $findProjectRootService = new FindProjectRootService();
        $watchingDir = $findProjectRootService->get($path);

        is_string($watchingDir) ?
            $this->setWatcher($watchingDir) :
            $this->invalidPathMessage($path);
    }

    private function setWatcher(string $path): void
    {
        $bootstrapper = new Bootstrapper();
        $bootstrapper->exec();
        $watcher = new Watcher();
        $processService = new ProcessService($path);
        $leastRecentEvent = new WatchEvent(
            0,
            '',
            EffectEventWatchEnum::ANY,
            PathTypeEnum::OTHER
        );

        $watcher->watchPath(dirname($path))
            ->onAnyChange(function (WatchEvent $watchEvent) use ($processService, &$leastRecentEvent): void {
                if ($watchEvent->effectTime - $leastRecentEvent->effectTime  > 1_000_000) {
                    $processService->resetProcess();

                    $this->restartMessage();
                }

                $leastRecentEvent = $watchEvent;
            });

        $watcher->setIntervalTime(0.5);

        $watcher->start();

        EventLoop::onSignal(SIGINT, function () use ($watcher, $processService) {
            $processService->stop();
            $watcher->stop();
            exit();
        });

        $this->successfulStartMessage($path);

        EventLoop::run();
    }

    private function successfulStartMessage(string $path): void
    {
        $this->render(<<<HTML
        <div class="py-2">
            <div class="px-1 bg-green-600">Brash PHP started in <b> watch </b> mode: </div>
            <em class="ml-1">
                {$path}
            </em>
        </div>
    HTML);
    }

    private function invalidPathMessage(string $path = ''): void
    {
        $includeMessage = $path === '' ? "No path provided" : "{$path} is invalid";
        $errorMessage = new ErrorMessage();

        $str = <<<HTML
                <b class="px-1 bg-red-500">Brash PHP could not start in watch mode</b>
                You must provide a valid path. {$includeMessage}
        HTML;

        $errorMessage($str);
    }

    private function restartMessage()
    {
        $this->render(<<<HTML
            <div class="py-2">
                <div class="px-1 bg-amber-500 text-white">Brash PHP is restarting in <b> watch </b> mode: </div>
            </div>
        HTML);
    }
}
