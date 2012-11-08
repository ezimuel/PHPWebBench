<?php
/* 
 * PHP Web Benchmark system
 * 
 * The command line script
 * 
 * @author    Enrico Zimuel (enrico@zimuel.it)
 * @link      http://github.com/ezimuel/PHPWebBench for the canonical source repository
 * @copyright Copyright (C) Enrico Zimuel
 * @license   BSD 3-Clause License http://opensource.org/licenses/BSD-3-Clause
 */

// Setup autoloading
include 'init_autoloader.php';

// Constants
define('VERSION', '0.1a');
define('DEFAULT_NUM', 3);
define('DEFAULT_CONCURRENCY', 1);

use Zend\Console;
use PHPWebBench\Bench;
use PHPWebBench\Utility;

$rules = array(
    'help|h'    => 'Get usage message',
    'bench|b-s' => 'Benchmark file to execute',
    'num|n-i'   => 'Number of total requests (default is 3)',
    'conc|c-i'  => 'Number of multiple requests to perform at a time (default is 1)',
    'url|u-s'   => 'Url to test (if no benchmakrk file specified)',
);

printf ("PHPWebBench - version %s by Enrico Zimuel\n", VERSION);

try {
    $opts = new Console\Getopt($rules);
    $opts->parse();
} catch (Console\Exception\RuntimeException $e) {
    echo $e->getUsageMessage();
    exit(2);
}

if ($opts->getOption('h')) {
    echo $opts->getUsageMessage();
    exit();
}

if (!isset($opts->b) && !isset($opts->u)) {
    echo "Error: you must specify a benchmark file or an URL to test\n";
    exit(2);
}

if (isset($opts->b) && !file_exists($opts->b)) {
    printf("Error: the benchmark file %s doesn't exist\n", $opts->b);
    exit(2);
}

if (isset($opts->b)) {
    $data = @include($opts->b);
    if ($data === false) {
        printf("Error: %s\n", error_get_last());
        exit(2);
    }
} else {
    $data = array(array(
            'name' => 'Testing URL',
            'url'  => $opts->u
    ));
}

$options = array();

$options['num'] = isset($opts->n) ? (int) $opts->n : DEFAULT_NUM;
$options['conc'] = isset($opts->c) ? (int) $opts->c : DEFAULT_CONCURRENCY;

$start = microtime(true);
$bench = new Bench($options);
$bench->setHttpAdapter('curl');
$bench->setBenchmark($data);
$result = $bench->execute();
$end    = microtime(true);

echo "\n";
printf("Number of requests  : %d\n", $options['num']);
printf("Concurrency level   : %d\n", $options['conc']);
printf("Time for tests      : %.2f sec\n", $end - $start);  
echo "\n";

foreach ($result as $test => $value) {
    printf("Test name           : %s\n", $test);
    printf("Complete requests   : %d\n", $value['success']);
    printf("Failed requests     : %d\n", $value['error']);
    $size_download = isset($value['size_download']) ? $value['size_download'] : 0;
    printf("HTML transferred    : %d bytes \n", $size_download);
    printf("Requests per second : %.2f [#/sec]\n", (1 / ($value['req_time'] / $options['conc'])));
    printf("Time per request    : %s (mean +/- %s)\n", 
            Utility::normalizeSec($value['req_time']), Utility::normalizeSec($value['req_time_std']));
    printf("Time per request    : %s (mean, across all concurrent requests)\n", 
            Utility::normalizeSec($value['req_time'] / $options['conc']));
    printf("Transfer rate       : %s/sec\n", Utility::normalizeByte($value['speed_download'] * $options['conc']));
    echo "\n";
}