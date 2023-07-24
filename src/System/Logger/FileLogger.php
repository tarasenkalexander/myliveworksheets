<?php
use System\Contracts\ILogger;
use System\Logger\LogLevel;

class FileLogger implements ILogger
{
    private static $path = "/var/logs/";

    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::Critical, $message, $context);
    }

    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::Error, $message, $context);
    }

    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::Warning, $message, $context);
    }

    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::Debug, $message, $context);
    }

    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::Info, $message, $context);
    }

    private function log(LogLevel $level, string|\Stringable $message, array $context = [])
    {
        $prefix = match ($level){
            LogLevel::Info => "[INFO] ",
            LogLevel::Debug => "[DEBUG] ",
            LogLevel::Warning => "[WARNING] ",
            LogLevel::Error => "[ERROR] ",
            LogLevel::Critical => "[CRITICAL] "
        };
        $date = date('Y-m-d');
        $finalMessage = $prefix . $message;
        file_put_contents(static::$path . $date . ".log", $finalMessage, FILE_APPEND);
    }
}