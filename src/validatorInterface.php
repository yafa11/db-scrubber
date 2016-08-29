<?php
namespace Scrubber;


/**
 * Class StrategyValidator
 */
interface validatorInterface
{
    /**
     * @param array $strategy
     * @return bool
     */
    public function validate($strategy);

    /**
     * @return string
     */
    public function getStatusMessage();
}