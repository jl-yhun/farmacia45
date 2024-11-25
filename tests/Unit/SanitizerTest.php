<?php

namespace Tests\Unit;

use App\Helpers\Sanitizer;
use App\Producto;
use PHPUnit\Framework\TestCase;

class SanitizerTest extends TestCase
{
    /**
     * @dataProvider testCases
     */
    public function test_when_called_return_correct_result($input, $expected)
    {
        $s = new Sanitizer();
        $actual = $s->rmAcentos()
            ->trim()
            ->doUpperCase()
            ->apply($input);

        $this->assertEquals($expected, $actual);
    }

    private function testCases(): array
    {
        return [
            ['Buen día a todos y Todas   ', 'BUEN DIA A TODOS Y TODAS'],
            ['PAÑALES HUGGIES VENTA SUELTA', 'PANALES HUGGIES VENTA SUELTA'],
            ['    NEIMICINA, CAOLÍN Y PECTINA SUSPENSIÓN', 'NEIMICINA, CAOLIN Y PECTINA SUSPENSION'],
            ['ÑAÑEÑIÑOÑU', 'NANENINONU'],
            ['ñandÚ', 'NANDU'],
        ];
    }
}
