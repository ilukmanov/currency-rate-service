<?php

namespace tests\request;

use PHPUnit\Framework\TestCase;
use app\request\CurrencyRequest;

/**
 * Тесты для CurrencyRequest.
 */
class CurrencyRequestTest extends TestCase
{
    /**
     * Тест, который проверяет успешное создание объекта CurrencyRequest.
     */
    public function testFromRequestSuccess(): void
    {
        $requestData = [
            'date' => '2024-09-25',
            'currency' => 'USD',
            'base_currency' => 'RUB'
        ];

        $currencyRequest = CurrencyRequest::fromRequest($requestData);

        $this->assertEquals('2024-09-25', $currencyRequest->date);
        $this->assertEquals('USD', $currencyRequest->currencyCode);
        $this->assertEquals('RUB', $currencyRequest->baseCurrency);
    }

    /**
     * Тест, который проверяет выброс исключения при некорректной дате.
     */
    public function testFromRequestInvalidDateFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class); // Используем полное имя класса
        $this->expectExceptionMessage('Некорректная дата: дата должна быть в формате Y-m-d.');

        $requestData = [
            'date' => 'invalid-date',
            'currency' => 'USD',
            'base_currency' => 'RUB'
        ];

        CurrencyRequest::fromRequest($requestData);
    }

    /**
     * Тест, который проверяет выброс исключения при дате в будущем.
     */
    public function testFromRequestDateInFuture(): void
    {
        $this->expectException(\InvalidArgumentException::class); // Используем полное имя класса
        $this->expectExceptionMessage('Некорректная дата: дата не может быть в будущем.');

        $requestData = [
            'date' => date('Y-m-d', strtotime('+1 day')),
            'currency' => 'USD',
            'base_currency' => 'RUB'
        ];

        CurrencyRequest::fromRequest($requestData);
    }

    /**
     * Тест, который проверяет выброс исключения при некорректном коде валюты.
     */
    public function testFromRequestInvalidCurrencyCode(): void
    {
        $this->expectException(\InvalidArgumentException::class); // Используем полное имя класса
        $this->expectExceptionMessage('Некорректный код валюты: код должен состоять из 3 букв.');

        $requestData = [
            'date' => '2024-09-25',
            'currency' => 'US', // Некорректный код валюты (менее 3 символов)
            'base_currency' => 'RUB'
        ];

        CurrencyRequest::fromRequest($requestData);
    }

    /**
     * Тест, который проверяет выброс исключения при некорректном коде базовой валюты.
     */
    public function testFromRequestInvalidBaseCurrencyCode(): void
    {
        $this->expectException(\InvalidArgumentException::class); // Используем полное имя класса
        $this->expectExceptionMessage('Некорректный код базовой валюты: код должен состоять из 3 букв.');

        $requestData = [
            'date' => '2024-09-25',
            'currency' => 'USD',
            'base_currency' => 'RU' // Некорректный код базовой валюты (менее 3 символов)
        ];

        CurrencyRequest::fromRequest($requestData);
    }

    /**
     * Тест, который проверяет, что коды валют преобразуются к верхнему регистру.
     */
    public function testFromRequestCurrencyCodesToUpperCase(): void
    {
        $requestData = [
            'date' => '2024-09-25',
            'currency' => 'usd',
            'base_currency' => 'rub'
        ];

        $currencyRequest = CurrencyRequest::fromRequest($requestData);

        $this->assertEquals('USD', $currencyRequest->currencyCode);
        $this->assertEquals('RUB', $currencyRequest->baseCurrency);
    }
}
