<?php

declare(strict_types=1);

namespace ChainCurrency\Domain;

class Chain
{
    /**
     * @var Block[]
     */
    private array $blocks = [];
    private array $clientAmounts = [];

    public function add(
        int $amount,
        int $sender,
        int $receiver,
        ?string $lastBlockHash = null,
    ): self {
        $block = new Block(
            $amount,
            $sender,
            $receiver,
            $lastBlockHash ?? $this->getLastBlock()?->getHash()
        );
        $this->blocks[] = $block;

        $this->initializeClient($block->getReceiver());
        $this->initializeClient($block->getSender());

        $this->clientAmounts[$block->getReceiver()] += $block->getAmount();
        if ($block->getReceiver() === $block->getSender()) {
            return $this;
        }

        $this->clientAmounts[$block->getSender()] -= $block->getAmount();

        return $this;
    }

    public function getLastBlock(): ?Block
    {
        $lastBlock = end($this->blocks);
        reset($this->blocks);
        if (!$lastBlock) {
            return null;
        }

        return $lastBlock;
    }

    private function initializeClient(int $client): void
    {
        if (isset($this->clientAmounts[$client])) {
            return;
        }
        $this->clientAmounts[$client] = 0;
    }

    public function isValid(): bool
    {
        /** @var ?Block $lastBlock */
        $lastBlock = null;
        foreach ($this->blocks as $block) {
            if ($lastBlock
                && $lastBlock->getHash() !== $block->getPreviousHash()
            ) {
                return false;
            }

            $lastBlock = $block;
        }
        return true;
    }

    public function getClientAmounts(): array
    {
        return $this->clientAmounts;
    }
}