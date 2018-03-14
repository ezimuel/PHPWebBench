<?php
/* 
 * PHP Web Benchmark system
 * 
 * Abstract Adapter
 * 
 * @author    Enrico Zimuel (enrico@zimuel.it)
 * @link      http://github.com/ezimuel/PHPWebBench for the canonical source repository
 * @copyright Copyright (C) Enrico Zimuel
 * @license   BSD 3-Clause License http://opensource.org/licenses/BSD-3-Clause
 */
namespace PHPWebBench\Adapter;

abstract class AbstractAdapter
{
    protected $options;
    
    protected $methods = array ('GET', 'POST', 'PUT', 'DELETE');
        
    protected $httpVersion;
    
    public function __construct($options = array()) 
    {
        if (!empty($options)) {
            $this->setOptions($options);
        }
    }
    
    public function setOptions($options) {
        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException("The options cannot be empty");
        }
        if (isset($options['header']) && !is_array($options['header'])) {
            throw new Exception\InvalidArgumentException("The header must be an array of string");
        }
        if (isset($options['data']) && !is_array($options['data'])) {
            throw new Exception\InvalidArgumentException("The data must be an array of key => value set");
        }
        if (isset($options['method'])) {
            $options['method'] = strtoupper ($options['method']);
            if (!in_array($options['method'], $this->methods)) {
                throw new Exception\InvalidArgumentException("The HTTP method must be a value");
            }
        }
        $this->options = $options;
    }
    
    public function setHttpVersion($version)
    {
        $this->httpVersion = $version;
    }
}
