<?php

namespace App\Twig\Runtime;

use Carbon\Carbon;
use Twig\Extension\RuntimeExtensionInterface;

class CarbonParserExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function carbonParse($value)
    {
        if ($value instanceof \DateTimeInterface) {
           return Carbon::parse($value);
        }
    }
}
