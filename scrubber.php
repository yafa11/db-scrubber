#!/usr/bin/php
<?php

use Scrubber\Scrubber;

require(__DIR__ . 'vendor/autoload.php');

if (PHP_SAPI !== 'cli') {
    echo "Not running under the command line";
    exit(1);
}


$scrubber = new Scrubber();
$options = getopt(null, array("table:","help","status"));
echo "Output is located in " . $scrubber->getLogFile() . PHP_EOL;
if (0 === count($options)) {
    $scrubber->scrub();
}
elseif (array_key_exists('status', $options)){
    $scrubber->scrubberStatus();
}
elseif (array_key_exists('table', $options)){
    $scrubber->setScrubOverride(true);
    $scrubber->scrubTableManually($options['table']);
}
else {
    echo 'Usage:' . PHP_EOL;
    echo "    php scrubber.php" . PHP_EOL;
    echo "Optional Parameters:" . PHP_EOL;
    echo "    --status" . PHP_EOL;
    echo "    --table Table Name which are listed below" . PHP_EOL;
    echo print_r($scrubber->getTables(), 1);
}

echo PHP_EOL . 'Done!' . PHP_EOL . PHP_EOL;
exit(0);










