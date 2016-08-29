<?php

namespace Scrubber;

use Logger;

/**
 * Class CliRunner
 * @package Scrubber
 */
class CliRunner
{
    /** @var   */
    private $config;
    /** @var  array */
    private $options;
    /** @var  processManagerInterface */
    private $processor;

    private $logFile;

    /** @var  CliRunner */
    private static $instance;

    protected function __construct(Logger $logger = null, processManagerInterface $processor = null){
        $this->logger = $logger instanceof Logger ? $logger : Log4phpFactory::getLogger();
        $this->processor = $processor instanceof processManagerInterface ? $processor : new ProcessManager();
    }

    public static function scrub($pathToStrategy = ''){
        $runner = CliRunner::getInstance();
        $runner->run();
    }

    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new CliRunner();
        }

        return static::$instance;
    }

    public function run(){
        $this->verifyCommandLine();
        $this->setOptions();
        $this->scrubUsingStrategy();

        echo "Scrubber has completed".PHP_EOL;
        echo $this->processor->getStatusMessage() . PHP_EOL;
        exit(0);
    }

    private function scrubUsingStrategy(){
        $strategyPath = str_replace('vendor/yafa11/scrubber-core/src','',__DIR__) .
            DIRECTORY_SEPARATOR . $this->options['strategy'];
        if(!is_file($strategyPath)){
            echo "The strategy {$strategyPath} could not be found". PHP_EOL;
            exit(1);
        }
        $strategy = include $strategyPath;
        $scrubberProcessResult = $this->processor->begin($strategy);

        if(!$scrubberProcessResult){
            echo $this->processor->getStatusMessage() . PHP_EOL;
            exit(1);
        }
    }

    private function verifyCommandLine(){
        if(PHP_SAPI !== 'cli'){
            echo "Not running under the command line". PHP_EOL;
            exit(1);
        }
        return true;
    }


    private function initializeConfig(){

    }

    private function setOptions(){
        $this->options = getopt(null, array("strategy:","help","status"));
        if(0 === count($this->options)) {
            echo 'Usage:' . PHP_EOL;
            echo "    php scrubber.php" . PHP_EOL;
            echo "Optional Parameters:" . PHP_EOL;
            echo "    --status" . PHP_EOL;
            echo "    --strategy <path to strategy file relative to this script>" . PHP_EOL;
            exit(0);
        }
    }
}
