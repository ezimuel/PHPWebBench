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

use PHPWebBench\Response;

class Curl extends AbstractAdapter
    implements AdapterInterface {
    
    const MAX_REDIRECT = 3;
    
    public function __construct($options = array())
    {
        parent::__construct($options);
        
        if (!function_exists( 'curl_init' )) {
            throw new Exception\InvalidArgumentException("I cannot use the CURL adapter, please install the CURL extension");
        }
    }
    
    /**
     * Send the HTTP request
     * 
     * @param  string $url
     * @param  integer $num
     * @param  integer $conc
     * @param  array $options
     * @return Response 
     */
    public function send($url, $num, $conc, $options = array()) {
        if (!empty($options)) {
            $this->setOptions($options);
        }
        
        $curl = array();
        for ($j=0; $j < $conc; $j++) {
            $curl[$j] = curl_init(); 
            $this->setCurlOption($curl[$j], $options);
            curl_setopt($curl[$j], CURLOPT_URL, $url);
        }
            
        $i         = 0;
        $result    = array();
        
        while ($i < $num) {
            $multi = curl_multi_init();
            foreach ($curl as $c) {
                curl_multi_add_handle($multi, $c);
            }
        
            // execute the handles
            $running = null;
            do {
                curl_multi_exec($multi, $running);
            } while ($running > 0);
            
            // get content and remove handles
            foreach ($curl as $c) {
                $result[] = $this->mapResponse(curl_getinfo($c));
                curl_multi_remove_handle($multi, $c);
            }
            curl_multi_close($multi);
            $i += $conc;
        }
        
        return $result;
    }
    
    protected function setCurlOption($curl, $options)
    {
        $method = (isset($options['method'])) ? strtoupper($this->options['method']) : 'GET';
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
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_MAXREDIRS, self::MAX_REDIRECT);
        switch ($method) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $options['data']);
                break;
            case 'PUT':
                break;
            case 'DELETE':
                break;
        }
        if (isset($this->options['header'])) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->options['header']);
        } 
        return $curl;
    }
 
    protected function mapResponse(array $data)
    {
        return array(
            'html_size'      => $data['size_download'],
            'time_request'   => $data['total_time'],
            'transfer_size'  => $data['size_download'] + $data['header_size'],
            'url'            => $data['url'],
            'status'         => $data['http_code'],
            'content_type'   => $data['content_type']
        );
    }
}
