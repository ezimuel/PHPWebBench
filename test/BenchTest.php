<?php
/*
 * PHP Web Benchmark system
 *
 * Test Bench
 *
 * @author    Enrico Zimuel (enrico@zimuel.it)
 * @link      http://github.com/ezimuel/PHPWebBench for the canonical source repository
 * @copyright Copyright (C) Enrico Zimuel
 * @license   BSD 3-Clause License http://opensource.org/licenses/BSD-3-Clause
 */
namespace PHPWebBenchTest;

use PHPWebBench\Bench;
use PHPUnit\Framework\TestCase;

class BenchTest extends TestCase
{
    public function setUp()
    {
        $this->bench = new Bench([
            'num'  => 10,
            'conc' => 2
        ]);
    }

    /**
     * @expectedException PHPWebBench\Exception\InvalidArgumentException
     */
    public function testExceptionOnConstruct()
    {
        $this->bench = new Bench();
    }

    public function testSetData()
    {
        $data = array(
            array(
                'name' => 'test',
                'url'  => 'http://www.google.com'
            )
        );
        $this->assertTrue($this->bench->setBenchmark($data) instanceof Bench);
    }

    public function testSetHTTPAdapter()
    {
        $this->assertTrue($this->bench->setHttpAdapter('curl') instanceof Bench);
    }

    /**
     * @expectedException PHPWebBench\Exception\RuntimeException
     */
    public function testSetWrongHTTPAdapter()
    {
        $this->bench->setHttpAdapter('pippo');
    }
}
