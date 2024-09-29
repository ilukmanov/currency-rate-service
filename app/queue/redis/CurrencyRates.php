<?php

namespace app\queue\redis;

use Webman\RedisQueue\Consumer;
use support\Container;
use app\repository\CurrencyRepository;
use Exception;
use support\Log;

/**
 * Потребитель очереди Redis для загрузки курсов валют.
 */
class CurrencyRates implements Consumer
{
    /** @var string Название очереди, которую нужно обрабатывать */
    public $queue = 'currency_rates';

    /** @var string Имя подключения */
    public $connection = 'default';

    /**
     * Метод для обработки задач из очереди.
     *
     * @param array $data Данные задачи из очереди.
     * @return void
     */
    public function consume($data)
    {
        // Получаем количество дней для загрузки курсов
        $days = $data['days'];

        // Получаем экземпляр CurrencyRepository через контейнер зависимостей
        $repository = Container::get(CurrencyRepository::class);

        // Текущая дата
        $currentDate = date('Y-m-d');

        // Загружаем курсы валют за каждый из указанных дней
        for ($i = 0; $i < $days; $i++) {
            $date = date('Y-m-d', strtotime("-$i days", strtotime($currentDate)));

            try {
                $repository->getRateFromExternalSource($date, 'USD');
                Log::info("Курс валюты за дату $date успешно загружен.");
            } catch (Exception $e) {
                Log::error("Ошибка при загрузке курса за дату $date: " . $e->getMessage());
            }

            // Задержка в 0.5 секунды
            usleep(500000);
        }
    }
}
