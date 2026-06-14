<?php

namespace MajesticStart\Cron;

/**
 * Interface CronTask
 * @package MajesticStart\Cron
 */
class CronTask
{
    /**
     * @var string
     */
    protected string $cronTaskIdentifier = "cron-task";

    /**
     * @param string $level
     * @param string $message
     */
    protected function log(string $level, string $message): void
    {
        $logFilePath = WRITEPATH . "logs/{$this->cronTaskIdentifier}-" . date("Y-m-d") . ".log";
        $logFile = fopen($logFilePath, "a");

        $pid = getmypid();
        $now = date("Y-m-d H:i:s");
        $message = str_replace(PHP_EOL, " ", $message);
        $level = strtoupper($level);

        fwrite($logFile, date("Y-m-d H:i:s") . "[{$pid}] {$now} {$level}: {$message}" . PHP_EOL);
        fclose($logFile);
    }

    /**
     * @param array $args
     */
    public function invoke($args = []): void
    {
        throw new \Exception("Not implemented");
    }
}
