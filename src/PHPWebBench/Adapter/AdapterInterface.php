<?php
/* 
 * PHP Web Benchmark system
 * 
 * Adapter interface
 * 
 * @author    Enrico Zimuel (enrico@zimuel.it)
 * @link      http://github.com/ezimuel/PHPWebBench for the canonical source repository
 * @copyright Copyright (C) Enrico Zimuel
 * @license   BSD 3-Clause License http://opensource.org/licenses/BSD-3-Clause
 */
namespace PHPWebBench\Adapter;

interface AdapterInterface {
    public function send($url, $num, $concurrency, $options = array());
}
