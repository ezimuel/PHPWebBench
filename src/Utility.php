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
     * @return array 
     */
    public static function normalizeByte($byte, $unit = null)
    {
        if ($unit === null) {
            if ($byte < 1024) {
                $unit = 'bytes';
            } elseif ($byte < 1048576) { // 1 Mb
                $unit = 'Kb';
            } elseif ($byte < 1073741824) { // 1 Gb
                $unit = 'Mb';
            } else {
                $unit = 'Gb';
            }
        }
        switch (ucfirst($unit)) {
            case 'Byte':
            case 'Bytes':
                $value = $byte;
                break;
            case 'Kb':
                $value = sprintf("%.2f", $byte / 1024);
                break;
            case 'Mb':
                $value = sprintf("%.2f", $byte / 1048576);
                break;
            case 'Gb':
                $value = sprintf("%.2f", $byte / 1073741824);
                break;
        }
        return array(
            'value' => $value,
            'unit'  => $unit
        );
    }
    /**
     * Normalize the second in milliseconds, second
     * 
     * @param  integer $sec
     * @return type 
     */
    public static function normalizeSec($sec, $unit = null)
    {
        if ($unit == null) {
            if ($sec < 1) {
                $value = sprintf("%.2f", $sec * 1000);
                $unit  = 'msec';
            } else {
                $value = sprintf("%.2f", $sec);
                $unit  = 'sec';
            }
        }
        switch (strtolower($unit)) {
            case 'second':
            case 'sec':
            case 'seconds':
                $value = sprintf("%.2f", $sec);
                break;
            case 'msec':
            case 'ms':
                $value = sprintf("%.2f", $sec * 1000);
                break;
        }
        return array(
            'value' => $value,
            'unit'  => $unit
        );
    }
}
