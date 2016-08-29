<?php
namespace Scrubber;

use Logger;

interface processManagerInterface
{
    public function begin($strategy, $config, Logger $logger);

    /**
     * @return string
     */
    public function getStatusMessage();
}