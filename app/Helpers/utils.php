<?php

use Yajra\DataTables\Facades\DataTables;

if (!function_exists('_color')) {
    function _color($hex = "", $c = 0)
    {
        $hex .= str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
        return $c >= 2 ? ("#" . $hex) : _color($hex, ++$c);
    }
}

if (!function_exists('datatable')) {
    function datatable($data)
    {
        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }
}
