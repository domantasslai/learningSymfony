<?php

namespace App\Service;

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class MarkdownHelper
{
    private $logger;

    public function __construct(
        private CacheInterface          $cache,
        private MarkdownParserInterface $markdown,
        private bool                    $isDebug,
        LoggerInterface                 $mdLogger
    )
    {
        $this->logger = $mdLogger;
    }

    public function parse(string $source): string
    {
        if (stripos($source, 'cat') !== false) {
            $this->logger->info('Moew!');
        }

        if ($this->isDebug) {
            return $this->markdown->transformMarkdown($source);
        }

        return $this->cache->get('markdown_' . md5($source), function () use ($source) {
            return $this->markdown->transformMarkdown($source);
        });
    }
}
