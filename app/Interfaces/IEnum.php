<?php

namespace App\Interfaces;

interface IEnum
{
    public static function values(): array;
    public static function label($value): string;
}
