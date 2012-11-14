<?php
/* 
 * PHP Web Benchmark system
 * 
 * Graph class
 * 
 * @author    Enrico Zimuel (enrico@zimuel.it)
 * @link      http://github.com/ezimuel/PHPWebBench for the canonical source repository
 * @copyright Copyright (C) Enrico Zimuel
 * @license   BSD 3-Clause License http://opensource.org/licenses/BSD-3-Clause
 */
namespace PHPWebBench;

use PHPWebBench\Utility;

class Graph
{
    protected static $supported_format = array('png', 'ps', 'pdf', 'jpg', 'svg', 'gif');
    
    protected $format = 'png';
       
    protected $terminal = 'png';
    
    protected $labelX = '';
    
    protected $labelY = '';
    
    protected $folder;
    
    protected $title;
    
    public function __construct($folder)
    {
        $this->folder = $folder;
        if (substr(exec('gnuplot -V'),0,7) !== 'gnuplot') {
            throw new Exception\RuntimeException("I need gnuplot (www.gnuplot.info) to generate the graphs.");
        }
    }
    
    public function setFormat($format)
    {
        $format = strtolower($format);
        if (!in_array($format, self::$supported_format)) {
            throw new Exception\InvalidArgumentException("The specified format $format is not supported");
        }
        $this->format = $format;
        switch ($format) {
            case 'ps':
                $this->terminal = 'postscript eps color';
                break;
            case 'png':
                $this->terminal = 'png';
                break;
            case 'gif':
                $this->terminal = 'gif';
                break;
            case 'svg':
                $this->terminal = 'svg';
                break;
            case 'jpg':
                $this->terminal = 'jpeg';
                break;
            case 'pdf':
                $this->terminal = 'pdf';
                break;
        }
    }
    
    public static function getSupportedFormat()
    {
        return self::$supported_format;
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function setLabelX($label)
    {
        $this->labelX = $label;
    }
    
    public function setLabelY($label)
    {
        $this->labelY = $label;
    }
    
    public function setData($data)
    {
        $this->data = $data;
    }
    
    public function draw($field)
    {
        if (empty($this->data)) {
            return false;
        }
        
        $output = '';
        $gp     = array();
        $tot    = 0;
        
        foreach ($this->data as $name => $value) {
            
            if (is_array($value[$field])) {
                $output .= sprintf("%s\t%s\t%s\n", $name, $value[$field][0], $value[$field][1]);
                $gp['select']   = '0:2:3';
                $gp['error']    = 'errorbars ';
                $gp['boxerror'] = 'with boxerrorbars linewidth 1';
            } else {
                $output .= sprintf("%s\t%s\n", $name, $value[$field]);
                $gp['select']   = '2';
                $gp['error']    = '';
                $gp['boxerror'] = '';
            }
            $tot++;
        }
        file_put_contents("{$this->folder}/$field.dat", $output);
        
        $plt = <<<EOD
reset
set term {$this->terminal}
set title "{$this->title}"
set output "{$this->folder}/$field.{$this->format}"
set grid
set yrange [0:]
set xrange [-1:$tot]
set xtic rotate by -45 scale 0 font "Verdana,10"
set nokey 
set ylabel "{$this->labelY}"
set xlabel "{$this->labelX}"
set style data histogram
set style histogram {$gp['error']}
set style fill solid 0.3 noborder
set bars front
set boxwidth 0.7 relative
plot "{$this->folder}/$field.dat" using {$gp['select']}:xtic(1) {$gp['boxerror']}
EOD;
        
        file_put_contents("{$this->folder}/$field.plt", $plt);
        
        exec("gnuplot {$this->folder}/$field.plt");   
        
    }
    
}
