<?php

namespace tests\service;

use PHPUnit\Framework\TestCase;
use app\service\CurrencyService;
use app\provider\CurrencyRateProvider;
use app\dto\CurrencyRateDTO;
use app\dto\CurrencyRateDifferenceDTO;
use app\helper\MathHelper;
use PHPUnit\Framework\MockObject\MockObject;
use Exception;

/**
 * Тесты для CurrencyService.
 */
class CurrencyServiceTest extends TestCase
{
    /** @var CurrencyRateProvider&MockObject Мок провайдера курсов валют */
    private CurrencyRateProvider|MockObject $rateProviderMock;

    /** @var CurrencyService Сервис для работы с валютами */
    private CurrencyService $currencyService;

    protected function setUp(): void
    {
        // Создаем мок для CurrencyRateProvider
        $this->rateProviderMock = $this->createMock(CurrencyRateProvider::class);

        // Создаем экземпляр CurrencyService с использованием мока
        $this->currencyService = new CurrencyService($this->rateProviderMock);
    }

    /**
     * Тест успешного получения курса и расчёта разницы.
     *
     * @return void
     */
    public function testGetRateWithDifferenceSuccess(): void
    {
        $date = '2023-09-25';
        $currencyCode = 'USD';
        $baseCurrency = 'RUB';
        $currentRate = 75.5;
        $previousRate = 74.0;
        $expectedDifference = MathHelper::subtract($currentRate, $previousRate);

        // Настройка мока для метода getRateData
        $this->rateProviderMock->method('getRateData')
            ->with($date, $currencyCode, $baseCurrency)
            ->willReturn(new CurrencyRateDTO($currentRate, $date));

        // Настройка мока для метода getPreviousRate
        $this->rateProviderMock->method('getPreviousRate')
            ->with($date, $currencyCode, $baseCurrency)
            ->willReturn(new CurrencyRateDTO($previousRate, $date));

        // Выполнение метода
        $result = $this->currencyService->getRateWithDifference($date, $currencyCode, $baseCurrency);

        // Проверка результата
        $this->assertInstanceOf(CurrencyRateDifferenceDTO::class, $result);
        $this->assertEquals($currentRate, $result->rate);
        $this->assertEquals($expectedDifference, $result->difference);
    }

    /**
     * Тест выброса исключения при отсутствии текущего курса.
     *
     * @return void
     */
    public function testGetRateWithDifferenceThrowsExceptionWhenCurrentRateNotFound(): void
    {
        $date = '2023-09-25';
        $currencyCode = 'USD';
        $baseCurrency = 'RUB';

        // Настройка мока для метода getRateData на выброс исключения
        $this->rateProviderMock->method('getRateData')
            ->with($date, $currencyCode, $baseCurrency)
            ->willThrowException(new Exception('Курс не найден'));

        // Проверка, что метод выбрасывает исключение
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Курс не найден');

        // Выполнение метода
        $this->currencyService->getRateWithDifference($date, $currencyCode, $baseCurrency);
    }

    /**
     * Тест выброса исключения при отсутствии курса за предыдущий день.
     *
     * @return void
     */
    public function testGetRateWithDifferenceThrowsExceptionWhenPreviousRateNotFound(): void
    {
        $date = '2023-09-25';
        $currencyCode = 'USD';
        $baseCurrency = 'RUB';
        $currentRate = 75.5;

        // Настройка мока для метода getRateData
        $this->rateProviderMock->method('getRateData')
            ->with($date, $currencyCode, $baseCurrency)
            ->willReturn(new CurrencyRateDTO($currentRate, $date));

        // Настройка мока для метода getPreviousRate на выброс исключения
        $this->rateProviderMock->method('getPreviousRate')
            ->with($date, $currencyCode, $baseCurrency)
            ->willThrowException(new Exception('Курс за предыдущий день не найден'));

        // Проверка, что метод выбрасывает исключение
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Курс за предыдущий день не найден');

        // Выполнение метода
        $this->currencyService->getRateWithDifference($date, $currencyCode, $baseCurrency);
    }
}
