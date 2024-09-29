<?php

namespace app\repository;

use app\cache\CurrencyCache;
use app\provider\FixedRateProvider;
use app\provider\CbrProvider;
use Exception;

/**
 * Репозиторий для получения курсов валют из кэша или провайдеров.
 */
class CurrencyRepository
{
    /** @var FixedRateProvider Провайдер фиксированных курсов. */
    protected FixedRateProvider $fixedRateProvider;

    /** @var CbrProvider Провайдер для работы с API ЦБР. */
    protected CbrProvider $cbrProvider;

    public function __construct(FixedRateProvider $fixedRateProvider, CbrProvider $cbrProvider)
    {
        $this->fixedRateProvider = $fixedRateProvider;
        $this->cbrProvider = $cbrProvider;
    }

    /**
     * Получить курс валюты (фиксированный или динамический).
     *
     * @param string $date Дата курса.
     * @param string $currencyCode Код валюты.
     *
     * @return array Курс и фактическая дата.
     * @throws Exception Если курс не найден.
     */
    public function getCurrencyRate(string $date, string $currencyCode): array
    {
        $fixedRate = $this->fixedRateProvider->getRate($currencyCode);
        if ($fixedRate !== null) {
            return ['rate' => $fixedRate, 'actual_date' => $date];
        }

        return $this->getRateFromExternalSource($date, $currencyCode);
    }

    /**
     * Получить курс из кэша или через API ЦБР.
     *
     * @param string $date Дата курса.
     * @param string $currencyCode Код валюты.
     *
     * @return array Курс и фактическая дата.
     * @throws Exception Если курс не найден.
     */
    public function getRateFromExternalSource(string $date, string $currencyCode): array
    {
        // Нормализуем дату в формат 'Y-m-d' для работы с кэшем
        $normalizedDate = date('Y-m-d', strtotime($date));

        // Проверяем наличие данных в кэше
        $cachedRates = CurrencyCache::getRates($normalizedDate);

        // Если данные в кэше есть, возвращаем их
        if ($cachedRates !== null && isset($cachedRates['rates'][$currencyCode])) {
            return [
                'rate' => $cachedRates['rates'][$currencyCode],
                'actual_date' => $cachedRates['actual_date'],
            ];
        }

        // Если данных в кэше нет, получаем их через провайдер ЦБР
        try {
            $rates = $this->cbrProvider->fetchCurrencyRates($date);
        } catch (Exception $e) {
            throw new Exception("Не удалось загрузить курс валюты с cbr.ru");
        }

        if ($rates === null || !isset($rates['rates'][$currencyCode])) {
            throw new Exception("Курс валюты {$currencyCode} не найден для даты: $date");
        }

        // Кэшируем данные и возвращаем курс
        CurrencyCache::cacheRates($normalizedDate, [
            'rates' => $rates['rates'],
            'actual_date' => $rates['date'],
        ]);

        return [
            'rate' => $rates['rates'][$currencyCode],
            'actual_date' => $rates['date'],
        ];
    }
}
