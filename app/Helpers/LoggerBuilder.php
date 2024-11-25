<?php

namespace App\Helpers;

interface LoggerBuilder
{
    public function module($value): LoggerBuilder;
    public function method($value): LoggerBuilder;
    // public function action($value): LoggerBuilder;
    public function exception($value): LoggerBuilder;
    public function description($value): LoggerBuilder;
    
    public function link_id($value): LoggerBuilder;
    public function user_id($value): LoggerBuilder;

    public function before($value): LoggerBuilder;
    public function after($value): LoggerBuilder;

    public function info($action = ''): LoggerBuilder;
    public function error($action = ''): LoggerBuilder;
    public function success($action = ''): LoggerBuilder;

    public function log();
}
