<?php

namespace Scrubber;


/**
 * Class StrategyValidator
 */
class StrategyValidator implements validatorInterface
{
    /** @var string  */
    private $statusMessage = '';

    public function __construct(){

    }

    /**
     * @param array $strategy
     * @return bool
     */
    public function validate($strategy){
        if(
            $this->validateArray($strategy) &&
            $this->validateStrategyAggregate($strategy) &&
            $this->validateStrategy($strategy)
        ){
            return true;
        }
        return false;
    }

    /**
     * @param array $strategy
     * @return bool
     */
    private function validateArray($strategy){
        if(!is_array($strategy)){
            $this->statusMessage = 'The requested strategy was not a properly formed PHP array';
            return false;
        }
        if(!isset($strategy['strategies']) && !isset($strategy['strategy'])){
            $this->statusMessage =
                'The requested strategy did not contain a "strategy" or "strategies" key: ' .
                PHP_EOL . print_r($strategy, 1)
            ;
            return false;
        }
        return true;
    }

    /**
     * @param array $strategyAggregate
     * @return bool
     */
    private function validateStrategyAggregate(array $strategyAggregate){
        $path = str_replace('vendor/yafa11/scrubber-core/src', '', __DIR__);
        if (isset($strategyAggregate['strategies'])) {
            foreach ($strategyAggregate['strategies'] as $importedStrategy) {
                if (!is_file($path . $importedStrategy)) {
                    $this->statusMessage = 'Could not locate the strategy file: ' . $path . $importedStrategy;
                    return false;
                }
                $subStrategy = include $path . $importedStrategy;
                $this->validate($subStrategy);
            }
        }
        return true;
    }

    /**
     * @param array $strategy
     * @return bool
     */
    private function validateStrategy(array $strategy){
        return true;
    }

    /**
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }


}

