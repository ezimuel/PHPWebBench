<?php
/* 
 * PHP Web Benchmark system
 * 
 * The statistical component
 * 
 * @author    Enrico Zimuel (enrico@zimuel.it)
 * @link      http://github.com/ezimuel/PHPWebBench for the canonical source repository
 * @copyright Copyright (C) Enrico Zimuel
 * @license   BSD 3-Clause License http://opensource.org/licenses/BSD-3-Clause
 */
namespace PHPWebBench;

class Stat
{
    /**
     * Compute the average of an array of numbers
     * 
     * @param  array $data
     * @return float 
     */
    public static function average($data)
    {
        $tot = 0;
        foreach ($data as $value) {
            $tot += $value;
        }
        return $tot / count($data);
    }
    /**
     * Compute the sample standard deviation 
     * 
     * @param  array $data
     * @return float 
     */
    public static function standard_deviation($data)
    {
        $avg = self::average($data);
        $tot = 0;
        foreach ($data as $value) {
            $x = $value - $avg;
            $tot += $x * $x;
        }
        return sqrt($tot / count($data));
    }
}
