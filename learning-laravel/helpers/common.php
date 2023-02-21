<?php

if(!function_exists('getFromDatemAttribute')){
    function getFromDatemAttribute($value,$format = 'd-m-Y H:i:s')
    {
        //goi ham nay bat khi noi dau
        return \Carbon\Carbon::parse($value)->format($format);
    }
}