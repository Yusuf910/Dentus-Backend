<?php

namespace App\Helpers;

class NumberToWords
{
    public static function convert($number)
    {
        $number = (int) $number;
        $hyphen      = ' ';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = array(
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'forty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
        );

        if ($number == 0) {
            return $dictionary[0];
        }

        $string = $fraction = null;

        // Handle negative numbers
        if ($number < 0) {
            $string = $negative;
            $number = abs($number);
        }

        // Convert the number to string to handle the decimal point
        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        // Convert the integer part
        if ($number >= 1000) {
            $string .= self::convert($number / 1000) . ' thousand';
            $number %= 1000;
        }

        if ($number >= 100) {
            $string .= $string ? $hyphen : '';
            $string .= self::convert($number / 100) . ' hundred';
            $number %= 100;
        }

        if ($number >= 20) {
            $string .= $string ? $hyphen : '';
            $string .= $dictionary[$number - $number % 10];
            $number %= 10;
        }

        if ($number > 0) {
            $string .= $string ? $hyphen : '';
            $string .= $dictionary[$number];
        }

        // Handle decimal part
        if ($fraction) {
            $string .= $decimal . $dictionary[$fraction];
        }

        return $string;
    }
}
