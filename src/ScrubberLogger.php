<?php

namespace Scrubber;


class ScrubberLogger
{
    /** @var  string */
    private $filePath;
    /** @var  boolean */
    private $verbose;

    public function __construct($pathToLog, $verbose = false){
        if(!is_file($pathToLog)){
            touch($pathToLog);
        }
        if(!is_writable($pathToLog)){
            throw new Exception('Could not write log file to ' . $pathToLog);
        }
        $this->verbose = $verbose == true;
        $this->filePath = $pathToLog;
    }

    public function log($message, $onlyShowIfVerbose = false){
        $date = date('Y-m-d H:i:s');
        if($onlyShowIfVerbose && $this->verbose === false){
            return;
        }
        file_put_contents($this->filePath, $date . ': '. $message . PHP_EOL, FILE_APPEND);
    }
}
