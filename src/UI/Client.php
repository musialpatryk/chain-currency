<?php

declare(strict_types=1);

namespace ChainCurrency\UI;

use ChainCurrency\Application\ChainRepository;
use ChainCurrency\Domain\Chain;
use ChainCurrency\Infrastructure\Json;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:client',
    description: 'Run single client instance'
)]
class Client extends Command
{
    private const SLEEP_TIME = 5;
    private const ACTION_CHECK_BALANCE = 'Check balance';
    private const ACTION_TRANSFER = 'Transfer amount';
    private const ACTION_CLOSE = 'Close';
    private ChainRepository $chainRepository;

    public function __construct()
    {
        parent::__construct();
        $this->chainRepository = new Json();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Chain currency client');

        do {
            $action = $io->choice(
                'What action do you want to perform?',
                [
                    self::ACTION_CHECK_BALANCE,
                    self::ACTION_TRANSFER,
                    self::ACTION_CLOSE,
                ],
                self::ACTION_CHECK_BALANCE
            );

            $chain = $this->chainRepository->get();
            if (!$chain->isValid()) {
                $io->error('Chain is corrupted, checking if its fixed...');
                sleep(self::SLEEP_TIME);
                continue;
            }

            match ($action) {
                self::ACTION_CHECK_BALANCE => $this->checkBalance($io, $chain),
                self::ACTION_TRANSFER => $this->transfer($io, $chain),
                self::ACTION_CLOSE => $io->info('Closing client!'),
                default => null,
            };
        } while (!isset($action) || $action !== self::ACTION_CLOSE);

        return Command::SUCCESS;
    }

    private function checkBalance(SymfonyStyle $io, Chain $chain): void
    {
        $table = $io->createTable();
        $table->setHeaders(['Client', 'Balance']);
        foreach ($chain->getClientAmounts() as $client => $amount) {
            $table->addRow([$client, $amount]);
        }
        $table->render();
    }

    private function transfer(SymfonyStyle $io, Chain $chain): void
    {
        $amount = $io->ask(
            'Amount (integer number):',
            '0',
            static fn(mixed $value) => (int) $value,
        );
        $sender = $io->ask(
            'Sender id (integer number):',
            '0',
            static fn(mixed $value) => (int) $value,
        );
        $receiver = $io->ask(
            'Receiver id (integer number):',
            '0',
            static fn(mixed $value) => (int) $value,
        );

        $chain->add(
            $amount,
            $sender,
            $receiver,
        );
        $this->chainRepository->append($chain);
    }
}