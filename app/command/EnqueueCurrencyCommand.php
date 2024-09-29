<?php

namespace app\command;

use Webman\RedisQueue\Redis;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда для добавления задачи на загрузку курсов валют в очередь.
 */
class EnqueueCurrencyCommand extends Command
{
    /** @var string Имя команды */
    protected static $defaultName = 'currency:enqueue';

    /**
     * Настройка команды.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Добавить задачу на загрузку курсов валют')
            ->addArgument('days', InputArgument::REQUIRED, 'Количество дней, за которые нужно загрузить курсы');
    }

    /**
     * Выполнение команды.
     *
     * @param InputInterface $input Входные аргументы.
     * @param OutputInterface $output Выходные данные.
     * @return int Статус выполнения команды.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $days = (int)$input->getArgument('days');

        if ($days <= 0) {
            $output->writeln('<error>Количество дней должно быть положительным числом.</error>');
            return Command::FAILURE;
        }

        // Добавляем задачу в очередь
        Redis::send('currency_rates', ['days' => $days]);

        $output->writeln("<info>Задача на загрузку курсов за последние $days дней успешно добавлена в очередь.</info>");
        return Command::SUCCESS;
    }
}
