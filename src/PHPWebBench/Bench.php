<?php
/* 
 * PHP Web Benchmark system
 * 
 * Bench class
 * 
 * @author    Enrico Zimuel (enrico@zimuel.it)
 * @link      http://github.com/ezimuel/PHPWebBench for the canonical source repository
 * @copyright Copyright (C) Enrico Zimuel
 * @license   BSD 3-Clause License http://opensource.org/licenses/BSD-3-Clause
 */
namespace PHPWebBench;

use PHPWebBench\Stat;

class Bench {
        
    protected $http;
    
    public function __construct($options = array()) 
    {
        if (empty($options)) {
            throw new Exception\InvalidArgumentException("Options cannot be empty");
        }
        $this->num_req = $options['num'];
        $this->concurrency = $options['conc'];
    }
    
    public function setHttpAdapter($adapter)
    {
        if (!file_exists(__DIR__ . '/Adapter/'. ucfirst($adapter) . '.php')) {
            throw new Exception\RuntimeException("The HTTP adapter doesn't exist");
        }
        $adapter = 'PHPWebBench\\Adapter\\' . ucfirst($adapter);
        $this->http = new $adapter();
        if (!($this->http instanceof \PHPWebBench\Adapter\AdapterInterface)) {
            throw new Exception\RuntimeException("The HTTP adapter must implement the HTTPInterface");
        }
        return $this;
    }
       
    public function setBenchmark($data)
    {
        if (empty($data)) {
            return false;
        }
        if (!is_array($data)) {
            throw new Exception\InvalidArgumentException("The benchmarking data must be specified as array");
        }
        foreach ($data as $bench) {
            if (is_array($bench)) {
                if (!isset($bench['name'])) {
                    throw new Exception\InvalidArgumentException("You need to specify the name of the benchmark test");
                }
                if (!isset($bench['url'])) {
                    throw new Exception\InvalidArgumentException("You need to specify the URL of the benchmark test");
                }
            }
        }
        $this->data = $data;
        return $this;
    }
    
    public function execute()
    {
        if (empty($this->data)) {
            return false;
        }
        if (empty($this->http)) {
            throw new Exception\InvalidArgumentException("I cannot execute the benchmark without a HTTP adapter");
        }
        $result = array();
        foreach ($this->data as $bench) {
            printf("Running test %s...", $bench['name']);
            $start = microtime(true);
            $response = $this->http->send($bench['url'], $this->num_req, $this->concurrency, $bench);
            $end = microtime(true);
            printf("done\n");
            $result[$bench['name']] = $this->analyzeResponse($response, $bench['url'], $end - $start);
        }
        return $result;
    }
    
    protected function analyzeResponse($result, $url, $time)
    {
        $output                  = array();
        $time_request            = array();
        $output['status']        = array();
        $output['transfer_size'] = 0;
        $success                 = 0;
        
        foreach ($result as $data) {
            $time_request[] = $data['time_request'];
            // Test for success, status code 2xx
            if (($data['status']>=200) && ($data['status']<300)) {
                if (!isset($output['content_type'])) {
                    $output['content_type']  = $data['content_type'];
                    $output['html_size']     = $data['html_size'];
                    $output['transfer_size'] = $data['transfer_size'];
                }
                $success++;
            } 
            if (isset($output['status'][$data['status']])) {
                $output['status'][$data['status']]++;
            } else {
                $output['status'][$data['status']] = 1;
            }
        }
        $output['url']             = $url;
        $output['total_transfer']  = $output['transfer_size'] * $success;
        $output['time_request']    = Stat::average($time_request);
        $output['time_request_sd'] = Stat::standard_deviation($time_request);
        $output['transfer_rate']   = $output['total_transfer'] / $time;
        $output['req_second']      = $success / $time;
        
        return $output;
    }
}
