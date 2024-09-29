<?php

namespace app\service;

use app\provider\CurrencyRateProvider;
use app\helper\MathHelper;
use app\dto\CurrencyRateDifferenceDTO;

/**
 * CurrencyService обрабатывает логику получения курсов валют и расчёта разницы.
 */
class CurrencyService
{
    /** @var CurrencyRateProvider Провайдер для получения курса валют */
    protected CurrencyRateProvider $rateProvider;

    /**
     * Конструктор сервиса для работы с валютами.
     *
     * @param CurrencyRateProvider $rateProvider Провайдер для получения курса валют.
     */
    public function __construct(CurrencyRateProvider $rateProvider)
    {
        $this->rateProvider = $rateProvider;
    }

    /**
     * Получить курс за указанную дату и рассчитать разницу с предыдущим днём.
     *
     * @param string $date Дата в формате 'Y-m-d'.
     * @param string $currencyCode Код валюты.
     * @param string $baseCurrency Код базовой валюты.
     * @return CurrencyRateDifferenceDTO Объект DTO с курсом и разницей.
     */
    public function getRateWithDifference(string $date, string $currencyCode, string $baseCurrency): CurrencyRateDifferenceDTO
    {
        // Получение текущего курса
        $currentRateData = $this->rateProvider->getRateData($date, $currencyCode, $baseCurrency);
        $currentRate = $currentRateData->rate;
        $currentActualDate = $currentRateData->actualDate;

        // Получение курса за предыдущий торговый день на основе фактической даты
        $previousRateData = $this->rateProvider->getPreviousRate($currentActualDate, $currencyCode, $baseCurrency);
        $previousRate = $previousRateData->rate;

        // Вычисление разницы между текущим и предыдущим курсом
        $difference = MathHelper::subtract($currentRate, $previousRate);

        return new CurrencyRateDifferenceDTO($currentRate, $difference);
    }
}
