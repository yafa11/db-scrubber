<?php

namespace Scrubber;

use mysqli;

/**
 * Class Scrubber
 */
class Scrubber
{
    /** @var  object[] */
    private $dbConnections;
    /** @var ContentFiller */
    private $contentFiller;

    /** @var bool */
    private $showUpdateStatements = false;
    /** @var bool */
    private $resetPasswords = true;
    /** @var bool */
    private $onlyProcessFreeTextClear = false;

    protected $scrubOverride = false;

    /**
     * @var bool
     */
    private $scrubDataManually = false;

    /** @var  array */
    private $config;
    /** @var int */
    private $startTime;
    /** @var  int */
    private $rowLimit;

    /**
     * @var string
     */
    protected $logFile;




    /**
     * Scrubber constructor.
     */
    public function __construct()
    {
        // set UTC default for strtotime functions used in this class;
        date_default_timezone_set('UTC');
        $this->startTime = time();
        $this->contentFiller = new ContentFiller;
        $this->loadConfigurations();
        $this->createScrubberTracker();
    }


    /**
     * @return string
     */
    public function getLogFile()
    {
        return $this->logFile;
    }

    /**
     * @param boolean $scrubOverride
     */
    public function setScrubOverride($scrubOverride)
    {
        $this->scrubOverride = $scrubOverride;
    }



    /**
     * Primary controlling method
     */
    public function scrub()
    {
        // @todo: You know, call something to scrub

        $this->statusMessage('Total Runtime', false);
    }


    /**
     *
     */
    private function loadConfigurations()
    {
        if (!is_file('scrubber.config')) {
            echo PHP_EOL . 'ERROR: Could not find configuration file "scrubber.config". Please create one from the "scrubber.config.example" file.' . PHP_EOL;
            exit(1);
        }

        $this->config = parse_ini_file('scrubber.config', true);

        if (isset($this->config['general']['resetPw'])) {
            $this->resetPasswords = ($this->config['general']['resetPw'] == true);
        }

        if (isset($this->config['general']['showSqlStatements'])) {
            $this->showUpdateStatements = ($this->config['general']['showSqlStatements'] == true);
        }

        if (isset($this->config['general']['onlyProcessFreeTextClear'])) {
            $this->onlyProcessFreeTextClear = ($this->config['general']['onlyProcessFreeTextClear'] == true);
        }

        if (isset($this->config['general']['scrubDataManually'])) {
            $this->scrubDataManually = ($this->config['general']['scrubDataManually'] == true);
        }

        $this->rowLimit = (isset($this->config['general']['rowLimit']) && intval($this->config['general']['rowLimit']) > 0) ? $this->config['general']['rowLimit'] : 10000;
        $timeLimit = (isset($this->config['general']['timeLimit']))? $this->config['general']['timeLimit'] : 0;
        $memLimit = (isset($this->config['general']['memLimit']))? $this->config['general']['memLimit'] : '1G';
        $this->logFile = (isset($this->config['general']['logfile']))? $this->config['general']['logfile'] : '/webpt/log/scrubber.log';
        ini_set('display_errors', 'On');
        ini_set('display_startup_errors', 'On');
        error_reporting(E_ALL);
        set_time_limit($timeLimit);
        ini_set('memory_limit', $memLimit);
    }

    /**
     *
     */
    private function createScrubberTracker() {
        $dbApp = $this->getMysqliDbConnection('');
        $this->createTrackerTable($dbApp);
        $this->fillTrackerTable($dbApp);
    }

    /**
     * @param mysqli $db
     */
    private function createTrackerTable(mysqli $db) {
        $sql = "CREATE TABLE IF NOT EXISTS `scrubber` (
          `table` VARCHAR(45) NULL,
          `start` DATETIME NULL DEFAULT NULL,
          `finish` DATETIME NULL DEFAULT NULL,
          PRIMARY KEY (`table`) );";
        $db->query($sql);
    }

    /**
     * @param mysqli $db
     */
    private function fillTrackerTable(mysqli $db) {
        foreach ($this->tables as $table) {
            $sql = "INSERT INTO scrubber (`table`) VALUES ('{$table}')";
            $db->query($sql);
        }
    }

    /**
     * Shows the status of different tables that are scrubbed
     */
    public function scrubberStatus() {
        $dbApp = $this->getMysqliDbConnection('');
        $sql = "SELECT * FROM scrubber where `start` is NULL";
        $results = $dbApp->query($sql);
        echo "Tables NOT STARTED:" . PHP_EOL;
        echo "    Table" . PHP_EOL;
        while ($row = mysqli_fetch_assoc($results))
        {
            echo "    {$row['table']}" . PHP_EOL;
        }
        $sql = "SELECT * FROM scrubber where `start` is not NULL and `finish` is NULL";
        $results = $dbApp->query($sql);
        echo "Tables STARTED:" . PHP_EOL;
        echo "    Table           Start" . PHP_EOL;
        while ($row = mysqli_fetch_assoc($results))
        {
            echo "    {$row['table']} {$row['start']}" . PHP_EOL;
        }
        $sql = "SELECT * FROM scrubber where `finish` is not NULL";
        $results = $dbApp->query($sql);
        echo "Tables FINISHED:" . PHP_EOL;
        echo "    Table           Finish" . PHP_EOL;
        while ($row = mysqli_fetch_assoc($results))
        {
            echo "    {$row['table']} {$row['finish']}" . PHP_EOL;
        }
    }



    /**
     * @param string $msg
     * @param bool $workingElipses
     */
    private function statusMessage($msg, $workingElipses = true)
    {
        $this->loggerConfig();
        $logger = Logger::getLogger('scrubber');
        $timeSinceStart = time() - $this->startTime;
        $hours = floor($timeSinceStart / 3600);
        $minutes = floor(($timeSinceStart / 60) % 60);
        $seconds = $timeSinceStart % 60;

        $formattedTimeSinceStart = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
        $elipses = ($workingElipses) ? '...' : '';
        $logger->info($formattedTimeSinceStart . '  ' . $msg . $elipses . PHP_EOL);
    }


    /**
     * @param string $databaseName
     * @return mysqli
     * @throws Exception
     */
    private function getMysqliDbConnection($databaseName){
        if(!isset($this->dbConnections['mysqli_'.$databaseName])){
            if(!isset($this->config[$databaseName])){
                throw new UnexpectedValueException($databaseName . ' connection information was not found in the config file');
            }
            $this->dbConnections['mysqli_'.$databaseName] = $this->loadDb($this->config[$databaseName]['host'], $this->config[$databaseName]['user'], $this->config[$databaseName]['password'], $this->config[$databaseName]['dbName']);
        }
        return $this->dbConnections['mysqli_'.$databaseName];
    }


    /**
     *
     */
    private function configuredFreeTextClear()
    {
        $mysqli = $this->getMysqliDbConnection('');
        $this->statusMessage('clearing free text fields as defined in scrubber.config');
        foreach($this->config['freeTextClear']['field'] as $field){
            $fieldArray = explode('.',$field);
            $sql = sprintf("UPDATE `%s` SET `%s` = ''", $fieldArray[0], $fieldArray[1]);
            if ($this->showUpdateStatements) {
                echo $sql . PHP_EOL;
            }
            $mysqli->query($sql);
            if(!empty($mysqli->error)){
                $this->statusMessage('     |- '.$mysqli->error);
            }
        }
    }




    /**
     * @param mysqli $dbConnector
     * @param $table
     * @param $fields
     * @return mysqli_result
     */
    private function pullData($dbConnector, $table, $fields)
    {
        $sql = 'SELECT ' . implode(', ', $fields) . ' FROM ' . $table;
        return $dbConnector->query($sql, MYSQLI_USE_RESULT);
    }


    /**
     * @param mysqli $dbConnection
     * @param string $table
     * @param string $idField
     * @param array $fields : A list of fields that need to be selected as their values affect the srubbed data output (exp birthdates, gender, status)
     * @param callable $scrubberCallBack
     */
    private function scrubTable(mysqli $dbConnection, $table, $idField, array $fields, $scrubberCallBack){
        if(in_array($table, $this->tables)){
            $started = $this->isTableStarted($dbConnection, $table);
            if(!empty($started) && !$this->scrubOverride) {
                $this->statusMessage($table . ' has already been started. Moving on to next table');
                return;
            }
        }
        $this->statusMessage('scrubbing '.$table);
        $offset = 0;
        $totalCount = 0;
        if(!in_array($idField, $fields)){
            $fields[] = $idField;
        }
        $fieldString = implode('`, `',$fields);
        $sql = "UPDATE `scrubber` SET `start` = '" . date('Y-m-d H:i:s') . "' WHERE `table` = '{$table}'";
        $dbConnection->query($sql);
        $sql = "SELECT count(*) FROM {$table}";
        $result = $dbConnection->query($sql);
        $totalRecords = reset(mysqli_fetch_row($result));
        do {
            $resultCount = 0;
            $sql = "SELECT `{$fieldString}` FROM `{$table}` LIMIT {$this->rowLimit} OFFSET {$offset}";
            $result = $dbConnection->query($sql);
            if($result){
                while ($row = $result->fetch_object()) {
                    $row = $scrubberCallBack($row, $this->contentFiller);
                    $this->updateData($dbConnection, $table, $idField, $row);
                    $resultCount++;
                }
                $offset += $this->rowLimit;
                $totalCount += $resultCount;
            }
            if($totalCount != $totalRecords) {
                $this->statusMessage('Processed '.$totalCount.' of '.$totalRecords.' rows for '.$table);
            }
        }while($resultCount != 0);
        $this->statusMessage('|---- scrubbed '.$table.' '.$totalCount .' rows processed.', false);
        $sql = "UPDATE `scrubber` SET `finish` = '" . date('Y-m-d H:i:s') . "' WHERE `table` = '{$table}'";
        $dbConnection->query($sql);
    }

    /**
     * @param mysqli $dbConnection
     * @param $table
     * @return mixed
     */
    private function isTableStarted(mysqli $dbConnection, $table) {
        $sql = "SELECT `start` from `scrubber` WHERE `table` = '{$table}'";
        $result = $dbConnection->query($sql);
        $result = $result->fetch_object();
        return $result->start;
    }



    /**
     * @param $host
     * @param $user
     * @param $password
     * @param $dbName
     * @return mysqli
     * @throws Exception
     */
    private function loadDb($host, $user, $password, $dbName)
    {
        $this->statusMessage("connecting to {$dbName} on {$host}");
        $objDatabase = new mysqli($host, $user, $password, $dbName);
        if (!isset($objDatabase) || null !== $objDatabase->connect_error) {
            throw new Exception("Could not connect to database '{$dbName}' on host '{$host}' using username '{$user}'");
            exit(1);
        }
        return $objDatabase;
    }


    /**
     * @param mysqli $dbConnector
     * @param $table
     * @param $idField
     * @param $dataCollection
     */
    private function updateData($dbConnector, $table, $idField, $dataCollection)
    {
        $setValues = array();
        foreach ($dataCollection as $field => $value) {
            if ($field != $idField) {
                $setValues[] = " {$field} = '".$dbConnector->real_escape_string($value)."'";
            }
        }
        $sql = 'UPDATE ' . $table . ' SET ' . implode(',', $setValues) . ' WHERE ' . $idField . ' = ' . $dataCollection->$idField;
        if ($this->showUpdateStatements) {
            echo $sql . PHP_EOL;
        }
        $dbConnector->query($sql);
    }

    /**
     * @param mysqli $dbConnector
     * @param $table
     * @param $dataCollection
     */
    private function insertData($dbConnector, $table, $dataCollection)
    {
        $fields = array();
        $values = array();
        foreach($dataCollection as $field => $value){
            $fields[] = $field;
            $values[] = "'" . $dbConnector->real_escape_string($value) . "'";
        }
        $sqlFields = implode(', ',$fields);
        $sqlValues = implode(', ',$values);
        $sql = "INSERT INTO {$table} ({$sqlFields}) VALUES ({$sqlValues})";
        if ($this->showUpdateStatements) {
            echo $sql . PHP_EOL;
        }
        $dbConnector->query($sql);
    }


    /**
     * @param $dbConnector
     * @param $tableName
     */
    private function truncateTable($dbConnector, $tableName)
    {
        $sql = 'TRUNCATE ' . $tableName;
        if ($this->showUpdateStatements) {
            echo $sql . PHP_EOL;
        }
        $dbConnector->query($sql);
    }

    /**
     * @return array
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * Logger Config
     */
    private function loggerConfig()
    {
        Logger::configure(array(
            'rootLogger' => array(
                'appenders' => array('default'),
            ),
            'appenders' => array(
                'default' => array(
                    'class' => 'LoggerAppenderFile',
                    'layout' => array(
                        'class' => 'LoggerLayoutSimple'
                    ),
                    'params' => array(
                        'file' => $this->logFile,
                        'append' => true
                    )
                )
            )
        ));
    }


}
