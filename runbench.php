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
    'url|u-s'   => 'URL to test (if benchmark file is not specified)',
);

printf ("PHPWebBench %s by Enrico Zimuel.\n\n", VERSION);

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
            'name' => $opts->u,
            'url'  => $opts->u
    ));
}

$options = array();
$options['num']  = isset($opts->n) ? (int) $opts->n : DEFAULT_NUM;
$options['conc'] = isset($opts->c) ? (int) $opts->c : DEFAULT_CONCURRENCY;

$bench = new Bench($options);
$bench->setHttpAdapter('curl');
$bench->setBenchmark($data);

$start  = microtime(true);
$result = $bench->execute();
$end    = microtime(true);

printf("\nRESULTS:\n");
printf("--------\n");

printf("Number of requests   : %d\n", $options['num']);
printf("Concurrency level    : %d\n", $options['conc']);
printf("Time for tests       : %.2f sec\n", $end - $start);  
printf("\n");

foreach ($result as $test => $value) {
    printf("Test name            : %s\n", $test);
    printf("URL                  : %s\n", $value['url']);
    foreach ($value['status'] as $status => $num) {
        printf("Status code %s      : %d\n", $status, $num);
    }    
    $html_size = isset($value['html_size']) ? $value['html_size'] : 0;
    $html_size = Utility::normalizeByte($html_size);
    printf("Document size        : %s [%s] \n", $html_size['value'], $html_size['unit']);
    $tot_transfer = Utility::normalizeByte($value['total_transfer']);
    printf("Total transferred    : %s [%s] \n", $tot_transfer['value'], $tot_transfer['unit']);
    printf("Requests per second  : %.2f [#/sec]\n", $value['req_second']);
    $time_request    = Utility::normalizeSec($value['time_request'], 'msec');
    $time_request_sd = Utility::normalizeSec($value['time_request_sd'], $time_request['unit']);
    printf(
        "Time per request     : %s ± %s [%s] (mean)\n", 
        $time_request['value'], $time_request_sd['value'], $time_request['unit']
    );
    if ($options['conc'] > 1) {
        $time_req_conc = Utility::normalizeSec($value['time_request'] / $options['conc']);
        printf(
            "Time per request     : %s [%s] (mean, across all concurrent requests)\n", 
            $time_req_conc['value'], $time_req_conc['unit']
        );
    }    
    $transfer_rate = Utility::normalizeByte($value['transfer_rate']);
    printf(
        "Transfer rate        : %s [%s/sec]\n",
        $transfer_rate['value'], $transfer_rate['unit']
    );
    printf("\n");
}