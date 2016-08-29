<?php

namespace Scrubber\MySqli;

use mysqli;
use Scrubber\Model\DatabaseConfig;
use Scrubber\statusDaoInterface;

class StatusDao implements statusDaoInterface
{
    /** @var mysqli  */
    private $dbAdapter;

    public function construct(DatabaseConfig $dbConnection){
        $this->dbadapter = new mysqli(
            $dbConnection->getHost(),
            $dbConnection->getUserName(),
            $dbConnection->getPassword()
        );
    }

    public function createSchemaIfNotExists($schema)
    {
        // TODO: Implement createSchemaIfNotExists() method.
    }

    public function createTableIfNotExists($table)
    {
        // TODO: Implement createTableIfNotExists() method.
    }

    public function addUpdatejob(array $job)
    {
        // TODO: Implement addUpdatejob() method.
    }

    public function getIncompleteJobs()
    {
        // TODO: Implement getIncompleteJobs() method.
    }

    public function getJobById($jobId)
    {
        // TODO: Implement getJobById() method.
    }

    public function insertStatus(array $status)
    {
        // TODO: Implement insertStatus() method.
    }

    public function updateProcessorStatus(array $status)
    {
        // TODO: Implement updateProcessorStatus() method.
    }

    public function getProcessorStatusByJobId($jobId)
    {
        // TODO: Implement getProcessorStatusByJobId() method.
    }

    public function purgeJobsOlderThan($date, $completeOnly = true)
    {
        // TODO: Implement purgeJobsOlderThan() method.
    }
}
