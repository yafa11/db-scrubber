<?php

namespace Scrubber;

use Logger;

class ProcessManager implements processManagerInterface
{
    /** @var string  */
    private $statusMessage = '';
    /** @var  validatorInterface */
    private $strategyValidator;

    private $strategy;

    public function __construct(
        validatorInterface $validator = null
    ){
        $this->strategyValidator = $validator instanceof validatorInterface ? $validator : new StrategyValidator();
    }

    public function begin($strategy, $config, Logger $logger){
        if(
            $this->loadConfigs() &&
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
