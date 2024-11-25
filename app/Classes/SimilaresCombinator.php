<?php

namespace App\Classes;

class SimilaresCombinator implements ISimilaresCombinator
{

    public function combine(array $products)
    {
        $n = count($products);
        $permutaciones = array();
        if ($n < 2) {
            return $permutaciones;
        }
        for ($i = 0; $i < $n - 1; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                $permutaciones[] = array($products[$i], $products[$j]);
                $permutaciones[] = array($products[$j], $products[$i]);
            }
        }
        return $permutaciones;
    }
}
