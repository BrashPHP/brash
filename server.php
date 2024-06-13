<?php

require_once __DIR__ . '/vendor/autoload.php';


use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\PhpSubprocess;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;

$process = new PhpSubprocess(['public/index.php'], env: getenv());
$p2 = clone $process;
$process->run(fn($e) => ($e));

while ($process->isRunning()) {
    // executes after the command finishes
    if (!$process->isSuccessful()) {
        throw new ProcessFailedException($process);
    }
}
echo $process->getOutput();

sleep(1);
// $process->stop(3);
// $process = $process->restart(fn($e) => print_r($e));

$p2->run(fn($e) => print_r($e));
while ($p2->isRunning()) {
    // executes after the command finishes
    if (!$p2->isSuccessful()) {
        throw new ProcessFailedException($p2);
    }
}

echo $p2->getOutput();


