<?php

namespace System\Logger;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use System\Contracts\ILogger;

class MonologLogger implements ILogger
{
    private Logger $logger;

    public function __construct(string $name)
    {
        $this->logger = new Logger($name);
        $this->logger->pushHandler(new StreamHandler(static::getLoggerName()));
    }

    private static function getLoggerName()
    {
        $todayDate = date('Y-m-d');
        return "var/logs/$todayDate.log";
    }

    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->logger->critical($message, $context);
    }

    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }
}