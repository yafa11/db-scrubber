<?php

namespace Scrubber;

use Psr\Log\LoggerInterface;

class ScrubberLogger implements LoggerInterface
{
    /** @var  string */
    private $filePath;
    /** @var  boolean */
    private $verbose;

    public function __construct($pathToLog, $verbose = false)
    {
        if (!is_file($pathToLog)) {
            mkdir(dirname($pathToLog), 0755, true);
            touch($pathToLog);
        }
        if (!is_writable($pathToLog)) {
            throw new Exception('Could not write log file to ' . $pathToLog);
        }
        $this->verbose = $verbose == true;
        $this->filePath = $pathToLog;
    }

    private function writeToLog($level, $message, $context = array(), $onlyShowIfVerbose = false)
    {
        $level = strtoupper($level);
        $date = date('Y-m-d H:i:s');
        if ($onlyShowIfVerbose && $this->verbose === false) {
            return;
        }
        file_put_contents(
            $this->filePath, $date . ': ' . $level . '- ' . $message . ' ' . print_r($context, 1) . PHP_EOL, FILE_APPEND
        );
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function emergency($message, array $context = array())
    {
        $this->writeToLog('emergency', $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function alert($message, array $context = array())
    {
        $this->writeToLog('alert', $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function critical($message, array $context = array())
    {
        $this->writeToLog('critical', $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function error($message, array $context = array())
    {
        $this->writeToLog('error', $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function warning($message, array $context = array())
    {
        $this->writeToLog('warning', $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function notice($message, array $context = array())
    {
        $this->writeToLog('notice', $message, $context, true);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function info($message, array $context = array())
    {
        $this->writeToLog('info', $message, $context, true);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function debug($message, array $context = array())
    {
        $this->writeToLog('debug', $message, $context, true);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        $verbose = false;
        if (in_array(strtolower($level), array('warning', 'notice', 'info', 'debug'))) {
            $verbose = true;
        }
        $this->writeToLog($level, $message, $context, $verbose);
    }
}
