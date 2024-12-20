<?php

namespace Tests\Unit;

use App\Classes\SimilaresCombinator;
use PHPUnit\Framework\TestCase;

class SimilaresCombinatorTest extends TestCase
{
    /**
     * @dataProvider testCases
     */
    public function test_combine_when_called_return_correct_values($input, $expected)
    {
        $imple = new SimilaresCombinator();

        $result = $imple->combine($input);

        $this->assertEquals($result, $expected);
    }

    private static function testCases()
    {
        return [
            [[47, 103], [[47, 103], [103, 47]]],
            [[47, 103, 64], [[47, 103], [103, 47], [47, 64], [64, 47], [103, 64], [64, 103]]],
            [
                [47, 103, 64, 11],
                [
                    [47, 103], [103, 47],
                    [47, 64], [64, 47],
                    [47, 11], [11, 47],
                    [103, 64], [64, 103],
                    [103, 11], [11, 103],
                    [64, 11], [11, 64],
                ]
            ],
            [
                [47, 103, 64, 11, 25, 46, 45, 90],
                [
                    [47, 103], [103, 47],
                    [47, 64], [64, 47],
                    [47, 11], [11, 47],
                    [47, 25], [25, 47],
                    [47, 46], [46, 47],
                    [47, 45], [45, 47],
                    [47, 90], [90, 47],
                    [103, 64], [64, 103],
                    [103, 11], [11, 103],
                    [103, 25], [25, 103],
                    [103, 46], [46, 103],
                    [103, 45], [45, 103],
                    [103, 90], [90, 103],
                    [64, 11], [11, 64],
                    [64, 25], [25, 64],
                    [64, 46], [46, 64],
                    [64, 45], [45, 64],
                    [64, 90], [90, 64],
                    [11, 25], [25, 11],
                    [11, 46], [46, 11],
                    [11, 45], [45, 11],
                    [11, 90], [90, 11],
                    [25, 46], [46, 25],
                    [25, 45], [45, 25],
                    [25, 90], [90, 25],
                    [46, 45], [45, 46],
                    [46, 90], [90, 46],
                    [45, 90], [90, 45]
                ]
            ],
            [[], []],
            [[1], []]
        ];
    }
}
