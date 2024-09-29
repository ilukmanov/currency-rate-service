<?php

namespace app\dto;

/**
 * DTO для представления курса валют и фактической даты.
 */
class CurrencyRateDTO
{
    /** @var float Курс валюты */
    public float $rate;

    /** @var string Фактическая дата */
    public string $actualDate;

    public function __construct(float $rate, string $actualDate)
    {
        $this->rate = $rate;
        $this->actualDate = $actualDate;
    }
}
