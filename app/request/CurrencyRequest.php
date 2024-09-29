<?php

namespace app\request;

use Respect\Validation\Validator as v;

/**
 * CurrencyRequest отвечает за валидацию и обработку параметров запроса для получения курсов валют.
 */
class CurrencyRequest
{
    /** @var string Дата запроса */
    public string $date;

    /** @var string Код валюты */
    public string $currencyCode;

    /** @var string Код базовой валюты */
    public string $baseCurrency;

    /**
     * Создает экземпляр CurrencyRequest на основе данных запроса.
     *
     * @param array $data Данные из запроса
     * @return self Валидированный экземпляр CurrencyRequest
     * @throws \InvalidArgumentException Если данные некорректны
     */
    public static function fromRequest(array $data): self
    {
        // Валидация даты
        $dateValidator = v::date('Y-m-d');
        if (!$dateValidator->validate($data['date'] ?? '')) {
            throw new \InvalidArgumentException('Некорректная дата: дата должна быть в формате Y-m-d.');
        }

        // Проверка, что дата не больше текущей
        $currentDate = date('Y-m-d');
        if ($data['date'] > $currentDate) {
            throw new \InvalidArgumentException('Некорректная дата: дата не может быть в будущем.');
        }

        // Валидация кода валюты
        $currencyValidator = v::alpha()->length(3, 3);
        if (!$currencyValidator->validate($data['currency'] ?? '')) {
            throw new \InvalidArgumentException('Некорректный код валюты: код должен состоять из 3 букв.');
        }

        // Валидация кода базовой валюты
        if (!$currencyValidator->validate($data['base_currency'] ?? '')) {
            throw new \InvalidArgumentException('Некорректный код базовой валюты: код должен состоять из 3 букв.');
        }

        // Создаем экземпляр CurrencyRequest
        $currencyRequest = new self();
        $currencyRequest->date = $data['date'];
        $currencyRequest->currencyCode = strtoupper($data['currency']);
        $currencyRequest->baseCurrency = strtoupper($data['base_currency']);

        return $currencyRequest;
    }
}
