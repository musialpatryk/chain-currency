<?php

declare(strict_types=1);

namespace ChainCurrency\Infrastructure;

use ChainCurrency\Application\ChainRepository;
use ChainCurrency\Domain\Chain;

class InMemory implements ChainRepository
{
    private Chain $chain;

    public function __construct()
    {
        $this->chain = (new Chain())
            ->add(1000, 1, 1)
            ->add(100, 1, 2);
    }

    public function get(): Chain
    {
        return $this->chain;
    }

    public function append(Chain $chain): void
    {
        $lastBlock = $chain->getLastBlock();
        if (!$lastBlock) {
            return;
        }
        $this->chain->add(
            $lastBlock->getAmount(),
            $lastBlock->getSender(),
            $lastBlock->getReceiver(),
        );
    }
}