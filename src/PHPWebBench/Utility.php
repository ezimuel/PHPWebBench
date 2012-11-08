<?php
/* 
 * PHP Web Benchmark system
 * 
 * Utility component
 * 
 * @author    Enrico Zimuel (enrico@zimuel.it)
 * @link      http://github.com/ezimuel/PHPWebBench for the canonical source repository
 * @copyright Copyright (C) Enrico Zimuel
 * @license   BSD 3-Clause License http://opensource.org/licenses/BSD-3-Clause
 */
namespace PHPWebBench;

class Utility {
    /**
     * Normalize the byte value in bytes, Kb, Mb or Gb
     * 
     * @param  integer $byte
     * @return string 
     */
    public static function normalizeByte($byte)
    {
        if ($byte < 1024) {
            return "$byte [bytes]";
        } elseif ($byte < 1048576) { // 1 Mb
            return sprintf("%.2f [Kb]", $byte / 1024);
        } elseif ($byte < 1073741824) { // 1 Gb
            return sprintf("%.2f [Mb]", $byte / 1048576);
        } else {
            return sprintf("%.2f [Gb]", $byte / 1073741824);
        }
    }
    public static function normalizeSec($sec)
    {
        if ($sec < 1) {
            return sprintf("%.2f [msec]", $sec * 1000);
        } else {
            return "$sec [sec]";
        }
    }
}
