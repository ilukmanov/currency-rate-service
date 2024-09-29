<?php

namespace app\dto;

/**
 * DTO для представления курса валют и разницы.
 */
class CurrencyRateDifferenceDTO
{
    /** @var float Текущий курс валюты */
    public float $rate;

    /** @var float Разница между текущим и предыдущим курсом */
    public float $difference;

    public function __construct(float $rate, float $difference)
    {
        $this->rate = $rate;
        $this->difference = $difference;
    }
}
