<?php

namespace Scrubber;

use Psr\Log\LoggerInterface;
use Scrubber\Model\ScrubberConfig;

class ProcessManager implements processManagerInterface
{
    /** @var string  */
    private $statusMessage = '';
    /** @var  validatorInterface */
    private $strategyValidator;
    /** @var  array */
    private $strategy;
    /** @var  ScrubberConfig */
    private $config;
    /** @var  LoggerInterface */
    private $logger;
    /** @var statusDaoInterface  */
    private $statusDao;

    public function __construct(
        ScrubberConfig $config,
        LoggerInterface $logger,
        validatorInterface $validator,
        statusDaoInterface $statusDao
    ){
        $this->config = $config;
        $this->logger = $logger;
        $this->strategyValidator = $validator;
        $this->statusDao = $statusDao;
    }

    public function begin($strategy){
        if(
            $this->validateStrategy($strategy) &&
            $this->createJob() &&
            $this->updateStatusTables() &&
            $this->processJob()
        ){
            return true;
        }
        return false;
    }

    /**
     * @param $strategy
     * @return bool
     */
    private function validateStrategy($strategy)
    {
        if(!$this->strategyValidator->validate($strategy)){
            $this->statusMessage = $this->strategyValidator->getStatusMessage();
            return false;
        }
        return true;
    }

    private function createJob()
    {
    }

    private function updateStatusTables()
    {
    }

    /**
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }


}
