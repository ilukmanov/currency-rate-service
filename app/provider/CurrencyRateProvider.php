<?php

namespace app\provider;

use app\repository\CurrencyRepository;
use app\dto\CurrencyRateDTO;
use Exception;

/**
 * CurrencyRateProvider отвечает за получение курса валют из репозитория.
 */
class CurrencyRateProvider
{
    /** @var CurrencyRepository Репозиторий для работы с курсами валют. */
    protected CurrencyRepository $repository;

    /**
     * Конструктор провайдера курсов валют.
     *
     * @param CurrencyRepository $repository Репозиторий для работы с курсами валют.
     */
    public function __construct(CurrencyRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Получить курс валюты за указанную дату.
     *
     * @param string $date Дата в формате 'Y-m-d'.
     * @param string $currencyCode Код валюты.
     * @param string $baseCurrency Код базовой валюты.
     * @return CurrencyRateDTO Объект DTO с курсом и фактической датой.
     * @throws Exception Если курс не найден или базовый курс равен нулю.
     */
    public function getRateData(string $date, string $currencyCode, string $baseCurrency): CurrencyRateDTO
    {
        // Получаем курс запрашиваемой валюты за указанную дату
        $currencyRate = $this->repository->getCurrencyRate($date, $currencyCode);
        $baseRate = $this->repository->getCurrencyRate($date, $baseCurrency);

        // Проверяем, что курсы найдены
        if (empty($currencyRate['rate']) || empty($baseRate['rate'])) {
            throw new Exception("Курс валюты или базовой валюты не найден для даты: $date");
        }

        if ($baseRate['rate'] == 0) {
            throw new Exception("Базовый курс валюты не может быть равен нулю для даты: $date");
        }

        return new CurrencyRateDTO(
            $currencyRate['rate'] / $baseRate['rate'],
            $currencyRate['actual_date']
        );
    }

    /**
     * Найти курс за предыдущий торговый день.
     *
     * @param string $currentActualDate Фактическая дата текущего курса.
     * @param string $currencyCode Код валюты.
     * @param string $baseCurrency Код базовой валюты.
     * @return CurrencyRateDTO Объект DTO с курсом и фактической датой.
     * @throws Exception Если курс не найден или базовый курс равен нулю.
     */
    public function getPreviousRate(string $currentActualDate, string $currencyCode, string $baseCurrency): CurrencyRateDTO
    {
        // Инициализируем предыдущую дату как день перед фактической датой
        $previousDate = date('Y-m-d', strtotime('-1 day', strtotime($currentActualDate)));

        // Цикл для поиска предыдущего торгового дня, пока не найдём валидные данные
        while (true) {
            // Получаем курс валюты и базовой валюты за предыдущий день
            $rateData = $this->repository->getCurrencyRate($previousDate, $currencyCode);
            $baseRateData = $this->repository->getCurrencyRate($previousDate, $baseCurrency);

            // Проверяем, что курсы найдены и корректны
            if (
                $rateData !== null && isset($rateData['rate']) && $rateData['rate'] !== null &&
                $baseRateData !== null && isset($baseRateData['rate']) && $baseRateData['rate'] !== null &&
                $rateData['actual_date'] !== $currentActualDate
            ) {
                if ($baseRateData['rate'] == 0) {
                    throw new Exception("Базовый курс валюты не может быть равен нулю для даты: $previousDate");
                }

                return new CurrencyRateDTO(
                    $rateData['rate'] / $baseRateData['rate'],
                    $rateData['actual_date']
                );
            }

            // Уменьшаем дату на один день и повторяем поиск
            $previousDate = date('Y-m-d', strtotime('-1 day', strtotime($previousDate)));
        }
    }
}
