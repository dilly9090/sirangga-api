<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function date_range($first, $last, $step = '+1 day', $output_format = 'd/m/Y' ) {

        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);

        while( $current <= $last ) {

            $dates[] = date($output_format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }

    public function readjson()
    {
        $json='[{"nama": "Sound system rapat", "idalat": "40", "jumlah": "0", "keterangan":"" },
                {"nama": "Infokus","idalat": "44", "jumlah": "2", "keterangan": "test"}, 
                {"nama": "xxx","idalat": "45", "jumlah": "22", "keterangan": "test"}]';

        $d=json_decode($json);
        foreach($d as $k=>$v)
        {
            foreach($v as $idx=>$val)
            {
                echo $idx.'-'.$val.'<br>';
            }
            echo '<br>';
        }
        // var_dump($d);
    }
}
