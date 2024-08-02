<?php

namespace Core\Services\Loggin;

use League\CLImate\CLImate;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\VarDumper\Caster\ScalarStub;

/** Print Monolog logs on the command line */
class CommandLineLogger extends StreamHandler
{
    private CLImate $climate;

    public function __construct(
        int|string|Level $level = Level::Debug,
        bool $bubble = true,
        ?int $filePermission = null,
        bool $useLocking = false
    ) {
        parent::__construct(
            'php://output',
            $level,
            $bubble,
            $filePermission,
            $useLocking
        );
        /** Set the default formatter to only print the message */
        $this->setFormatter(new LineFormatter("%message%\n"));
        $this->climate = new CLImate();
    }

    /**
     * Invoked every time a log is written
     */
    protected function write(LogRecord $record): void
    {
        /** Bail early if not on the command line */
        if (!$this->isOnCommandLine()) {
            return;
        }

        /** Handle log records with context */
        if (!empty($record->context)) {
            $record->formatted = "$record->message: ";
            parent::write($record);
            $this->dump($record->context);
            return;
        }

        /** Handle simple log records */
        parent::write($record);
    }

    /**
     * Check if currently on the command line
     */
    private function isOnCommandLine(): bool
    {
        return in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true);
    }

    /**
     * Dump something using Symphony's VarDumper
     */
    private function dump(mixed ...$vars): void
    {
        if (!$vars) {
            $this->climate->dump(new ScalarStub('🐛'));
            return;
        }

        if (array_key_exists(0, $vars) && count($vars) === 1) {
            $this->climate->dump(var: $vars[0]);
        } else {
            foreach ($vars as $key => $value) {
                $this->climate->dump([$key => $value]);
            }
        }
    }
}
