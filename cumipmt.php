<?php
/**
 * 测试数据参考 https://github.com/abetomo/xlsxfin.rs/blob/main/src/lib.rs
 */


function pmt($rate, $periods, $present, $future=0, $type=0){
    if(!is_numeric($rate) || !is_numeric($periods)){
        return 0;
    }

    if($rate == 0){
        return -(($present+$future)/$periods);
    }else{
        $term = pow((1+$rate), $periods);
        var_dump($term);
        if($type == 1){
            return -(($future*$rate/($term-1)+$present*$rate/(1-1/$term))/(1+$rate));
        }else{
            return -($future*$rate/($term-1)+$present*$rate/(1-1/$term));
        }
    }
}

//$result = pmt(0, 36, 100, 0, false);
//$result = pmt(0.3, 36, 100, 0, true);
//var_dump($result);
//exit;

function fv($rate, $periods, $payment, $value=0, $type=0){
    if( empty($periods)){
        return 0;
    }

    if($rate == 0){
        $fv = $value+$payment*$periods;
    }else{
        $term = pow((1+$rate), $periods);
        if($type == 1){
            $fv = $value*$term + $payment*(1+$rate)*($term-1)/$rate;
        }else{
            $fv = $value*$term + $payment*($term-1)/$rate;
        }
    }

    return -$fv;
}

$result = fv(0, 12, 10000, 0, 1);

//var_dump($result);exit;

function cumipmt($rate, $periods, $value, $start, $end, $type){
    //todo 转成数字，以及判断是否有误

    if($rate<=0 || $periods<=0 || $value<=0){
        return 0;
    }

    if($start<1 || $end<1 || $start>$end){
        return 0;
    }

    if($type !=0 && $type !=1){
        return 'wrong type';
    }

    $payment = pmt($rate, $periods, $value, 0, $type);
    $interest = 0;

    if($start == 1){
        if($type == 0){
            $interest = -$value;
            $start++;
        }
    }

    for ($i=$start; $i<=$end; $i++){
        if($type == 1){
            $interest += fv($rate, $i-2, $payment, $value, 1) - $payment;
        }else{
//            var_dump($rate, $i, $payment, $value);exit;
            $interest += fv($rate, $i-1, $payment, $value, 0);
        }
    }

    return $interest * $rate;
}

$result = cumipmt(0.09, 30, 125000, 13, 24, 0);
$result = cumipmt(-1.0, 36, 800000, 6, 12, 0);
$result = cumipmt(0.1, 36, 800000, 6, 12, 1);
$result = cumipmt(0.1, 36, 800000, 6, 12, 0);
$result = cumipmt(0.1, 36, 800000, 1, 12, 0);
$result = cumipmt(0.015, 31.57, 2000, 1, 31.57, 0);
var_dump($result);
