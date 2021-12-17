<?php

namespace Sterzik\ChristmasTree;

class TreeController
{
    private $tree;
    private $treeProxy;

    private $proxyUrl;

    public function __construct(string $proxyUrl)
    {
        $this->tree = new ChristmasTree();
        $this->treeProxy = new ChristmasTreeProxy($this->tree);
        $this->proxyUrl = $this->getFullProxyUrl($proxyUrl);
    }

    private function getFullProxyUrl(string $proxyUrl): string
    {
        $delim = (strpos($proxyUrl, '?') === false) ? '?' : '&';
        return $proxyUrl . $delim . "read=1";
    }

    public function run($outputFd = null): void
    {
        $handler = function($signal){
            $this->tree->clearOutput();
            exit;
        };
        pcntl_async_signals(true);
        pcntl_signal(SIGTERM, $handler);
        pcntl_signal(SIGINT, $handler);
        $this->tree->render($outputFd);
        while (true) {
            $content = @file_get_contents($this->proxyUrl);
            if (!is_string($content)) {
                continue;
            }
            $queue = @json_decode($content, true);
            if (!is_array($queue)) {
                continue;
            }
            foreach ($queue as $command) {
                $this->invokeCommand($command);
            }
            $this->tree->render($outputFd);
            usleep(300000);
        }
    }

    private function invokeCommand($command): void
    {
        if (!is_array($command) || empty($command)) {
            return;
        }

        $command = array_values($command);

        $method = array_shift($command);

        if (!is_string($method)) {
            return;
        }

        try {
            $this->treeProxy->$method(...$command);
        } catch (Throwable $e) {
        }
    }
}
