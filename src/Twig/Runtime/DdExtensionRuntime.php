<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class DdExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function dd($value)
    {
        dd($value);
    }

    public function dump($value)
    {
        dump($value);
    }
}
