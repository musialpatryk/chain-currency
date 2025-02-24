<?php

declare(strict_types=1);

namespace ChainCurrency\Infrastructure;

use ChainCurrency\Application\ChainRepository;
use ChainCurrency\Domain\Chain;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class Json implements ChainRepository
{
    private const CHAIN_PATH = 'var/chain';
    private const NEW_LINE_PATTERN = '/\r\n|\n|\r/';
    private const AMOUNT_KEY = 'amount';
    private const SENDER_KEY = 'sender';
    private const RECEIVER_KEY = 'receiver';
    private const HASH_KEY = 'previousHash';

    private Filesystem $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    public function get(): Chain
    {
        try {
            $content = $this->filesystem->readFile(self::CHAIN_PATH);
            $contentEntries = preg_split(self::NEW_LINE_PATTERN, $content);

            $chain = new Chain();
            foreach ($contentEntries as $entry) {
                if (empty($entry)) {
                    continue;
                }

                $blockData = json_decode($entry, true, 512, JSON_THROW_ON_ERROR);
                $chain->add(
                    (int)$blockData[self::AMOUNT_KEY],
                    (int)$blockData[self::SENDER_KEY],
                    (int)$blockData[self::RECEIVER_KEY],
                    (string)$blockData[self::HASH_KEY],
                );
            }
            return $chain;
        } catch (IOException) {
            return new Chain();
        }
    }

    public function append(Chain $chain): void
    {
        $lastBlock = $chain->getLastBlock();
        if (!$lastBlock) {
            return;
        }

        $this->filesystem->appendToFile(
            self::CHAIN_PATH,
            json_encode($lastBlock) . PHP_EOL,
        );
    }
}