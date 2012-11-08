<?php
/* 
 * PHP Web Benchmark system
 * 
 * The CURL HTTP adapter
 * 
 * @author    Enrico Zimuel (enrico@zimuel.it)
 * @link      http://github.com/ezimuel/PHPWebBench for the canonical source repository
 * @copyright Copyright (C) Enrico Zimuel
 * @license   BSD 3-Clause License http://opensource.org/licenses/BSD-3-Clause
 */
namespace PHPWebBench\Adapter;

class Curl extends AbstractAdapter
    implements AdapterInterface {
    
    public function __construct($options = array())
    {
        parent::__construct($options);
        
        if (!function_exists( 'curl_init' )) {
            throw new Exception\InvalidArgumentException("I cannot use the CURL adapter, please install the CURL extension");
        }
    }
    
    public function send($url, $num, $conc, $options = array()) {
        if (!empty($options)) {
            $this->setOptions($options);
        }
        
        $curl = array();
        for ($j=0; $j < $conc; $j++) {
            $curl[$j] = curl_init(); 
            $this->setCurlOption($curl[$j], $options);
            curl_setopt($curl[$j], CURLOPT_URL, $url);
            curl_setopt($curl[$j], CURLOPT_RETURNTRANSFER, true);
        }
            
        $i = 0;
        $result = array();
        $result['tot_time'] = 0;
        
        while ($i < $num) {
            $multi = curl_multi_init();
            foreach ($curl as $c) {
                curl_multi_add_handle($multi, $c);
            }
        
            // execute the handles
            $running = null;
            $start = microtime(true);
            do {
                curl_multi_exec($multi, $running);
            } while ($running > 0);
            $end = microtime(true);
            $result['tot_time'] += $end - $start;

            // get content and remove handles
            foreach ($curl as $c) {
                $result['data'][] = curl_getinfo($c);
                curl_multi_remove_handle($multi, $c);
            }
            curl_multi_close($multi);
            $i += $conc;
        }
        
        return $result;
    }
    
    protected function setCurlOption($curl, $options)
    {
        $method = (isset($options['method'])) ? $this->options['method'] : 'GET';
        if (isset($this->options['data'])) {
            if ($method == 'GET') {
                $query = '';
                foreach ($this->options['data'] as $key => $value) {
                    $query .= '&' . urlencode($key) . '=' . urlencode($value);
                }
                if (strpos($url,'?') === false) {
                    $url .= '?' . substr($query,1);
                } else {
                    $url .= $query;
                }
            }
        }
        switch ($method) {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, true );
                curl_setopt($curl, CURLOPT_POSTFIELDS, $options['data']);
                break;
            case 'PUT':
                break;
            case 'DELETE':
                break;
        }
        if (isset($this->options['header'])) {
            curl_setopt($curl, CURLOPT_HEADER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->options['header']);
        } else {
            curl_setopt($curl, CURLOPT_HEADER, false);
        }
        return $curl;
    }
    
}
