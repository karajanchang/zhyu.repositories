<?php


namespace Zhyu\Tools;


namespace Zhyu\Tools;

//-----數值負轉正，正轉負
class PlusMinusConvert
{
    public function run(int $number = 0){

        return $number > 0 ? -1 * $number : abs($number);
    }

}