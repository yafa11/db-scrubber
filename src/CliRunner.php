<?php

namespace Scrubber;

use Psr\Log\LoggerInterface;
use Scrubber\Model\ScrubberConfig;

/**
 * Class CliRunner
 * @package Scrubber
 */
class CliRunner
{
    /** @var  ScrubberConfig */
    private $config;
    /** @var  array */
    private $options;
    /** @var  processManagerInterface */
    private $processor;
    /** @var  string */
    private $logFilePath;


    public function __construct(
        LoggerInterface $logger,
        processManagerInterface $processor,
        ScrubberConfig $config
    ) {
        $this->verifyCommandLine();
        $this->setOptions();
        $this->verifyConfig($config);

        $this->logger = $logger instanceof LoggerInterface ? $logger :
            new ScrubberLogger($this->logFilePath, $this->config->isVerboseLogging());
        $this->processor = $processor instanceof processManagerInterface ?
            $processor : new ProcessManager($this->config, $this->logger);
    }


    public function run()
    {
        if ($this->logger instanceof ScrubberLogger) {
            echo 'Writing log entries to ' . $this->logFilePath . PHP_EOL;
        }
        if (isset($this->options['strategy'])) {
            echo 'Executing strategy ' . $this->options['strategy'] . PHP_EOL;
            exit(0);
            $this->scrubUsingStrategy($this->options['strategy']);
        } elseif (isset($this->options['status'])) {
            echo 'Getting incomplete jobs...' . PHP_EOL;
            exit(0);
            $this->getJobStatus();
        } elseif (isset($this->options['continue'])) {
            echo 'Attempting to complete job ID ' . $this->options['continue'] . PHP_EOL;
            exit(0);
            $this->getJobStatus();
        }


        echo '-----------------------' . PHP_EOL .
            'Scrubber has completed' . PHP_EOL . '-----------------------' . PHP_EOL;
        echo $this->processor->getStatusMessage() . PHP_EOL;
        exit(0);
    }

    private function scrubUsingStrategy()
    {
        $strategyPath = str_replace(self::PATH_FROM_VENDOR, '', __DIR__) .
            DIRECTORY_SEPARATOR . $this->options['strategy'];
        if (!is_file($strategyPath)) {
            echo "The strategy {$strategyPath} could not be found" . PHP_EOL;
            exit(1);
        }
        $strategy = include $strategyPath;
        $scrubberProcessResult = $this->processor->begin($strategy);

        if (!$scrubberProcessResult) {
            echo $this->processor->getStatusMessage() . PHP_EOL;
            exit(1);
        }
    }

    private function verifyCommandLine()
    {
        if (PHP_SAPI !== 'cli') {
            echo "Not running under the command line" . PHP_EOL;
            exit(1);
        }

        return true;
    }


    private function setOptions()
    {
        $this->options = getopt(null, array("strategy:", "status", "continue:"));
        if (0 === count($this->options)) {
            echo '! No valid action specified' . PHP_EOL;
            $this->displayHelp();
        }
    }

    private function displayHelp()
    {
        echo 'Standard usage:' . PHP_EOL;
        echo '    php scrubber.php --strategy strategies/example.php' . PHP_EOL;
        echo 'Actions:' . PHP_EOL;
        echo '    --status | Displays current status of incomplete jobs' . PHP_EOL;
        echo '    --strategy <file> | Scrubs the table(s) as defined in the strategy file. <file> is relative to ' .
            'the application root' . PHP_EOL;
        echo '    --continue <job_id> | Attempts to complete the remaining tasks of an incomplete job' . PHP_EOL;
        exit(0);
    }

    private function verifyConfig(ScrubberConfig $config)
    {

        if (0 === count($config->getDbConnections())) {
            $configPath = $config->getDatabaseConfigPath();
            echo 'Error: There are no configured database connections. Please add connections to ' . $configPath .
                ' and make sure the file is readable' . PHP_EOL;
            exit(1);
        }

        if ($config->isUsingDefaults()) {
            echo 'WARNING: Configuration not laoded. Could not read the configuration file at ' .
                $config->getConfigPath() . PHP_EOL;
            $this->promptToContinue('Would you like to continue using the default values?');
        }

        $this->config = $config;
    }

    private function promptToContinue($message = 'Would you like to continue?')
    {
        echo $message . ' Enter "y" to continue' . PHP_EOL;
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        if (strtolower(trim($line)) !== 'y') {
            echo 'ABORTING!' . PHP_EOL;
            exit(0);
        }
        fclose($handle);
        echo 'Thank you, continuing...' . PHP_EOL;
    }
}
