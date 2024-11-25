<?php
namespace App\Classes;

interface IProveedoresExtractor {
    function extract($codigo): array;
}