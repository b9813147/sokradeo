<?php

namespace App\Exports;

class IndexStatisticsMap
{
    private $feature_list = [
        'T'     => 0,
        'T.1'   => null,
        'T.1.1' => 0,
        'T.1.2' => 0,
        'T.1.3' => 0,
        'T.1.4' => 0,
        'T.1.5' => 0,
        'T.2'   => null,
        'T.2.1' => 0,
        'T.2.2' => 0,
        'T.2.3' => 0,
        'T.2.4' => 0,
        'T.2.5' => 0,
        'T.3'   => null,
        'T.3.1' => 0,
        'T.3.2' => 0,
        'T.3.3' => 0,
        'T.3.4' => 0,
        'T.4'   => null,
        'T.4.1' => 0,
        'T.4.2' => 0,
        'T.4.3' => 0,
        'T.5'   => null,
        'T.5.1' => 0,
        'T.5.2' => 0,
        'T.5.3' => 0,
        'T.5.4' => 0,
        'P'     => 0,
        'P.1'   => 0,
        'P.2'   => 0,
        'P.3'   => 0,
        'P.4'   => 0,
        'P.5'   => 0,
        'P.6'   => 0,
        'C'     => 0,
        'C.1'   => 0,
        'C.2'   => 0,
        'C.3'   => 0,
        'C.4'   => 0,
        'C.5'   => 0,
    ];

    private $type_feature_map = [
        '47' => 'T',
        '30' => 'T.1.1',
        '38' => 'T.1.2',
        '31' => 'T.1.3',
        '29' => 'T.1.4',
        '45' => 'T.1.5',
        '17' => 'T.2.1',
        '16' => 'T.2.2',
        '28' => 'T.2.3',
        '18' => 'T.2.4',
        '19' => 'T.2.5',
        '21' => 'T.3.1',
        '23' => 'T.3.2',
        '22' => 'T.3.3',
        '24' => 'T.3.4',
        '25' => 'T.3.4',
        '26' => 'T.3.4',
        '27' => 'T.3.4',
        '1'  => 'T.4.1',
        '6'  => 'T.4.2',
        '7'  => 'T.4.2',
        '8'  => 'T.4.2',
        '9'  => 'T.4.2',
        '10' => 'T.4.2',
        '11' => 'T.4.2',
        '12' => 'T.4.2',
        '13' => 'T.4.2',
        '20' => 'T.4.3',
        '15' => 'T.5.1',
        '14' => 'T.5.2',
        '41' => 'T.5.3',
        '46' => 'T.5.4',
        '48' => 'P',
        '49' => 'P.1',
        '50' => 'P.2',
        '53' => 'P.3',
        '61' => 'P.4',
        '52' => 'P.5',
        '54' => 'P.6',
        '55' => 'C',
        '56' => 'C.1',
        '57' => 'C.2',
        '58' => 'C.3',
        '59' => 'C.4',
        '60' => 'C.5',
    ];
    
    private $idx_list = [
        '47', '48', '49', '50', '53', '61', '52', '54', '55', '56', '57', '58', '59', '60',
    ];

    private $idx_decimal_list = [
        '49', '50', '53', '61', '52', '54', '56', '57', '58', '59', '60',
    ];

    public function __construct()
    {
        
    }
    
    public function get_feature_list()
    {
        return $this->feature_list;
    }

    public function get_feature_name($type)
    {
        return (isset($this->type_feature_map[$type])) ? $this->type_feature_map[$type] : false;
    }
    
    public function get_sum_result($feature_list)
    {
        $v = $feature_list;
        $v['T.1'] = $v['T.1.1'] + $v['T.1.2'] + $v['T.1.3'] + $v['T.1.4'] + $v['T.1.5'];
        $v['T.2'] = $v['T.2.1'] + $v['T.2.2'] + $v['T.2.3'] + $v['T.2.4'] + $v['T.2.5'];
        $v['T.3'] = $v['T.3.1'] + $v['T.3.2'] + $v['T.3.3'] + $v['T.3.4'];
        $v['T.4'] = $v['T.4.1'] + $v['T.4.2'] + $v['T.4.3'];
        $v['T.5'] = $v['T.5.1'] + $v['T.5.2'] + $v['T.5.3'] + $v['T.5.4'];
        
        return $v;
    }
    
    public function check_if_idx_type($type)
    {
        return (in_array($type, $this->idx_list)) ? true : false;
    }

    public function check_if_idx_decimal_type($type)
    {
        return (in_array($type, $this->idx_decimal_list)) ? true : false;
    }
}
