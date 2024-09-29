<?php

namespace app\provider;

/**
 * Возвращает фиксированные курсы для определённых валют, например, RUR.
 */
class FixedRateProvider
{
    /**
     * Возвращает фиксированный курс для указанной валюты.
     *
     * @param string $currencyCode Код валюты (например, 'RUR').
     * @return float|null Возвращает 1.0 для RUR или null для других валют.
     */
    public function getRate(string $currencyCode): ?float
    {
        return strtoupper($currencyCode) === 'RUR' ? 1.0 : null;
    }
}
