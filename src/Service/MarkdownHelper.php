<?php

namespace App\Service;

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Cache\CacheInterface;

class MarkdownHelper
{
    private $logger;

    public function __construct(
        private CacheInterface          $cache,
        private MarkdownParserInterface $markdown,
        private bool                    $isDebug,
        private Security                $security,
        LoggerInterface                 $mdLogger,
    )
    {
        $this->logger = $mdLogger;
    }

    public function parse(string $source): string
    {
        if (stripos($source, 'cat') !== false) {
            $this->logger->info('Moew!');
        }

        if ($this->security->isGranted('IS_AUTHENTICATED_REMEMBERED')) { // OR $this->security->getUser()
            $this->logger->info('Rendering markdown for {user}', [
                'user' => $this->security->getUser()->getEmail()
            ]);
        }

        if ($this->isDebug) {
            return $this->markdown->transformMarkdown($source);
        }

        return $this->cache->get('markdown_' . md5($source), function () use ($source) {
            return $this->markdown->transformMarkdown($source);
        });
    }
}
