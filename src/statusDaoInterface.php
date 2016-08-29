<?php

namespace Scrubber;

interface statusDaoInterface
{
    public function createSchemaIfNotExists($schema);

    public function createTableIfNotExists($table);

    public function addUpdatejob(array $job);

    public function getIncompleteJobs();

    public function getJobById($jobId);

    public function insertStatus(array $status);

    public function updateProcessorStatus(array $status);

    public function getProcessorStatusByJobId($jobId);

    public function purgeJobsOlderThan($date, $completeOnly = true);
}
