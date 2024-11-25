<?php

namespace App\Helpers;

interface SanitizerBuilder
{
    public function rmAcentos(): SanitizerBuilder;
    public function trim(): SanitizerBuilder;
    public function doUpperCase(): SanitizerBuilder;

    public function apply(string $value): string;
}
