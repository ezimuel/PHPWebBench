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
            $response = $this->http->send($bench['url'], $this->num_req, $this->concurrency);
            $result[$bench['name']] = $this->analyzeResult($response['data'], $response['tot_time']);
        }
        return $result;
    }
    
    protected function analyzeResult($result, $time)
    {
        $total_time        = array();
        $speed_download    = array();
        $output            = array();
        $tot_size          = 0;
        $output['success'] = 0;
        $output['error']   = 0;
        
        foreach ($result as $data) {
            $total_time[] = $data['total_time'];
            $speed_download[] = $data['speed_download'];
            $tot_size += $data['size_download'];
            if ($data['http_code'] === 200) {
                $output['content_type']  = $data['content_type'];
                $output['size_download'] = $data['size_download'];
                $output['header_size']   = $data['header_size'];
                $output['success']++;
            } else {
                $output['error']++;
            }
        }

        $output['req_time']           = Stat::average($total_time);
        $output['req_time_std']       = Stat::standard_deviation($total_time);
        $output['speed_download']     = Stat::average($speed_download);
        $output['speed_download_std'] = Stat::standard_deviation($speed_download);
        $output['transfer_rate']      = $tot_size / $time;
        
        return $output;
    }
}
