<?php

namespace Scrubber\Model;

use Scrubber\Constants;

class ScrubberConfig
{
    /** @var  bool */
    private $showSqlStatements = false;
    /** @var int  */
    private $timeLimit = 0;
    /** @var string  */
    private $memoryLimit = '1G';
    /** @var int  */
    private $rowLimit = 10000;
    /** @var int */
    private $numberOfProcesses = 10;
    /** @var string  */
    private $logfile = '/data/log/scrubber.log';
    /** @var string  */
    private $scrubberSchemaName = 'scrubber';
    /** @var string  */
    private $scrubberSchemaDbConneciton = 'default';
    /** @var string  */
    private $contentFillerClass = '\Scrubber\ContentFiller';
    /** @var DatabaseConfig[] */
    private $dbConnections = array();
    /** @var bool  */
    private $verboseLogging = false;


    /** @var bool */
    private $usingDefaults = false;
    /** @var  string */
    private $configPath;
    /** @var  string */
    private $dbConfigPath;

    public function __construct(array $dbConfigArray = array(), array $configArray = array()){
        $this->configPath = str_replace(Constants::PATH_FROM_VENDOR, '', __DIR__) . Constants::DATABASE_CONFIG_NAME;
        $this->dbConfigPath = str_replace(Constants::PATH_FROM_VENDOR, '', __DIR__) . Constants::DATABASE_CONFIG_NAME;
        if(0 === count($configArray)){
            $configArray = $this->loadFromConfigFile($this->configPath);
        }
        if(0 === count($dbConfigArray)){
            $dbConfigArray = $this->loadFromConfigFile($this->dbConfigPath);
        }
        $this->load($configArray, $dbConfigArray);
    }

    /**
     * @return boolean
     */
    public function isShowSqlStatements()
    {
        return $this->showSqlStatements == true;
    }

    /**
     * @param boolean $showSqlStatements
     */
    public function setShowSqlStatements($showSqlStatements)
    {
        $this->showSqlStatements = $showSqlStatements;
    }

    /**
     * @return int
     */
    public function getTimeLimit()
    {
        return $this->timeLimit;
    }

    /**
     * @param int $timeLimit
     */
    public function setTimeLimit($timeLimit)
    {
        $this->timeLimit = $timeLimit;
    }

    /**
     * @return string
     */
    public function getMemoryLimit()
    {
        return $this->memoryLimit;
    }

    /**
     * @param string $memoryLimit
     */
    public function setMemoryLimit($memoryLimit)
    {
        $this->memoryLimit = $memoryLimit;
    }

    /**
     * @return int
     */
    public function getRowLimit()
    {
        return $this->rowLimit;
    }

    /**
     * @param int $rowLimit
     */
    public function setRowLimit($rowLimit)
    {
        $this->rowLimit = $rowLimit;
    }

    /**
     * @return int
     */
    public function getNumberOfProcesses()
    {
        return $this->numberOfProcesses;
    }

    /**
     * @param int $numberOfProcesses
     */
    public function setNumberOfProcesses($numberOfProcesses)
    {
        $this->numberOfProcesses = $numberOfProcesses;
    }

    /**
     * @return string
     */
    public function getLogfile()
    {
        return $this->logfile;
    }

    /**
     * @param string $logfile
     */
    public function setLogfile($logfile)
    {
        $this->logfile = $logfile;
    }

    /**
     * @return string
     */
    public function getScrubberSchemaName()
    {
        return $this->scrubberSchemaName;
    }

    /**
     * @param string $scrubberSchemaName
     */
    public function setScrubberSchemaName($scrubberSchemaName)
    {
        $this->scrubberSchemaName = $scrubberSchemaName;
    }

    /**
     * @return string
     */
    public function getScrubberSchemaDbConneciton()
    {
        return $this->scrubberSchemaDbConneciton;
    }

    /**
     * @param string $scrubberSchemaDbConneciton
     */
    public function setScrubberSchemaDbConneciton($scrubberSchemaDbConneciton)
    {
        $this->scrubberSchemaDbConneciton = $scrubberSchemaDbConneciton;
    }

    /**
     * @return string
     */
    public function getContentFillerClass()
    {
        return $this->contentFillerClass;
    }

    /**
     * @param string $contentFillerClass
     */
    public function setContentFillerClass($contentFillerClass)
    {
        $this->contentFillerClass = $contentFillerClass;
    }

    /**
     * @return DatabaseConfig[]
     */
    public function getDbConnections()
    {
        return $this->dbConnections;
    }

    /**
     * @param DatabaseConfig[] $dbConnections
     */
    public function setDbConnections(array $dbConnections)
    {
        $this->dbConnections = array();
        foreach($dbConnections as $dbConnection){
            $this->addDbConnection($dbConnection);
        }
    }

    /**
     * @param DatabaseConfig $dbConfig
     */
    public function addDbConnection(DatabaseConfig $dbConfig){
        $this->dbConnections[] = $dbConfig;
    }

    /**
     * @return boolean
     */
    public function isVerboseLogging()
    {
        return $this->verboseLogging == true;
    }

    /**
     * @param boolean $verboseLogging
     */
    public function setVerboseLogging($verboseLogging)
    {
        $this->verboseLogging = $verboseLogging;
    }


    /** --------------------------- LOADING LOGIC --------------------------- */

    /**
     * @return boolean
     */
    public function isUsingDefaults()
    {
        return $this->usingDefaults;
    }

    /**
     * @return string
     */
    public function getConfigPath()
    {
        return $this->configPath;
    }

    /**
     * @return string
     */
    public function getDatabaseConfigPath()
    {
        return $this->dbConfigPath;
    }


    private function loadFromConfigFile($configPath)
    {
        if(is_readable($configPath)){
            return parse_ini_file($configPath);
        }
        return array();
    }


    private function load($config, $dbconfig){
        if(0 === count($config)){
            $this->usingDefaults = true;
        }
    }
}
