<?php

declare(strict_types=1);

namespace ChainCurrency\Domain;

readonly class Block implements \JsonSerializable
{
    private const AMOUNT = 'amount';
    private const SENDER = 'sender';
    private const RECEIVER = 'receiver';
    private const PREVIOUS_HASH = 'previousHash';

    public function __construct(
        private int $amount,
        private int $sender,
        private int $receiver,
        private ?string $previousHash = null,
    ) {
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getSender(): int
    {
        return $this->sender;
    }

    public function getReceiver(): int
    {
        return $this->receiver;
    }

    public function getHash(): string
    {
        return hash('sha256', json_encode($this->toArray()));
    }

    public function getPreviousHash(): ?string
    {
        return $this->previousHash;
    }

    public function toArray(): array
    {
        return [
            self::AMOUNT => $this->amount,
            self::SENDER => $this->sender,
            self::RECEIVER => $this->receiver,
        ];
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            $this->toArray(),
            [
                self::PREVIOUS_HASH => $this->getPreviousHash(),
            ],
        );
    }
}