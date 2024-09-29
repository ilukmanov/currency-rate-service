<?php

namespace app\controller;

use support\Request;
use app\service\CurrencyService;
use app\request\CurrencyRequest;
use Exception;

/**
 * CurrencyController отвечает за получение и возврат данных о курсе валют.
 */
class CurrencyController
{
    /** @var CurrencyService Сервис для работы с валютами. */
    protected CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Получить курс валюты на определённую дату и рассчитать разницу с предыдущим днём.
     *
     * @param Request $request HTTP-запрос.
     * @return mixed Ответ с данными о курсе.
     */
    public function getCurrencyRate(Request $request)
    {
        try {
            // Извлечение и валидация данных из запроса
            $currencyRequest = CurrencyRequest::fromRequest($request->all());

            // Получаем данные о курсе с использованием CurrencyService
            $rateData = $this->currencyService->getRateWithDifference(
                $currencyRequest->date,
                $currencyRequest->currencyCode,
                $currencyRequest->baseCurrency
            );
        } catch (Exception $e) {
            return error([$e->getMessage()], 400);
        }

        return success($rateData);
    }
}
