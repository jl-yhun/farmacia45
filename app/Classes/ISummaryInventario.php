<?php

namespace App\Classes;

interface ISummaryInventario
{
    public function calculateDiff($items, $inDb): array;
    public function finish($diff);
}
