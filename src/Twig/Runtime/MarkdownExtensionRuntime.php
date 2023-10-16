<?php

namespace App\Twig\Runtime;

use App\Service\MarkdownHelper;
use Twig\Extension\RuntimeExtensionInterface;

class MarkdownExtensionRuntime implements RuntimeExtensionInterface
{
    private MarkdownHelper $markdownHelper;

    public function __construct(MarkdownHelper $markdownHelper)
    {
        $this->markdownHelper = $markdownHelper;
    }

    public function parseMarkdown($value)
    {
        return $this->markdownHelper->parse($value);
    }
}
