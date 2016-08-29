<?php

namespace Scrubber;

use Psr\Log\LoggerInterface;
use Scrubber\Model\ScrubberConfig;
use Scrubber\MySqli\StatusDao;

/**
 * Class CliRunnerFactory
 * Extend this class to build out the scrubber with your own custom dependancies
 * @package Scrubber
 */
class CliRunnerFactory
{
    /** @var self */
    protected static $instance;

    protected function __construct()
    {
    }

    /**
     * @return CliRunner
     */
    public static function getCliRunner()
    {
        if (!isset(self::$instance)) {
            self::$instance = new CliRunnerFactory();
        }

        return self::$instance->buildCliRunner();
    }

    /**
     * @return CliRunner
     */
    public function buildCliRunner()
    {
        $config = $this->getConfig();
        $logger = $this->buildLogger($config);
        try {
            $processManager = $this->buildProcessManager($config, $logger);

            return new CliRunner($logger, $processManager, $config);
        } catch (Exception $ex) {
            $logger->error('Could not build CliRunner: ' . $ex->getMessage(), $ex->getTrace());
            throw new Exception('Could not build CliRunner', 0, $ex);
        }
    }

    /**
     * @return ScrubberConfig
     */
    public function getConfig()
    {
        return new ScrubberConfig();
    }

    /**
     * @param ScrubberConfig $config
     * @return LoggerInterface
     */
    protected function buildLogger(ScrubberConfig $config)
    {
        $logFilePath = str_replace(Constants::PATH_FROM_VENDOR, '', __DIR__) . $config->getLogfile();

        return new ScrubberLogger($logFilePath, $config->isVerboseLogging());
    }

    /**
     * @param ScrubberConfig $config
     * @param LoggerInterface $logger
     * @return processManagerInterface
     */
    protected function buildProcessManager(ScrubberConfig $config, LoggerInterface $logger)
    {
        $strategyValidator = $this->getStrategyValidator();
        $statusDao = $this->getStatusDao($config);

        return new ProcessManager($config, $logger, $strategyValidator, $statusDao);
    }

    /**
     * @return validatorInterface
     */
    protected function getStrategyValidator()
    {
        return new StrategyValidator();
    }

    /**
     * @return statusDaoInterface
     */
    protected function getStatusDao($config)
    {
        return new StatusDao($config);
    }
}
