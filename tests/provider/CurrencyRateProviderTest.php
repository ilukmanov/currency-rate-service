<?php

namespace tests\provider;

use PHPUnit\Framework\TestCase;
use app\provider\CurrencyRateProvider;
use app\repository\CurrencyRepository;
use app\dto\CurrencyRateDTO;

/**
 * Тесты для CurrencyRateProvider.
 */
class CurrencyRateProviderTest extends TestCase
{
    /** @var CurrencyRepository&\PHPUnit\Framework\MockObject\MockObject */
    private $repositoryMock;

    /** @var CurrencyRateProvider */
    private CurrencyRateProvider $provider;

    protected function setUp(): void
    {
        // Создаем мок репозитория валют
        $this->repositoryMock = $this->createMock(CurrencyRepository::class);

        // Создаем экземпляр CurrencyRateProvider с мокнутым репозиторием
        $this->provider = new CurrencyRateProvider($this->repositoryMock);
    }

    /**
     * Тест успешного получения курса валюты за указанную дату.
     */
    public function testGetRateDataSuccess(): void
    {
        $date = '2024-09-25';
        $currencyCode = 'USD';
        $baseCurrency = 'RUB';

        // Настраиваем мок репозитория для возврата курсов валют
        $this->repositoryMock
            ->method('getCurrencyRate')
            ->willReturnMap([
                [$date, $currencyCode, ['rate' => 75.5, 'actual_date' => $date]],
                [$date, $baseCurrency, ['rate' => 1.0, 'actual_date' => $date]]
            ]);

        // Выполняем метод
        $result = $this->provider->getRateData($date, $currencyCode, $baseCurrency);

        // Проверка результата
        $this->assertInstanceOf(CurrencyRateDTO::class, $result);
        $this->assertEquals(75.5, $result->rate);
        $this->assertEquals($date, $result->actualDate);
    }

    /**
     * Тест выброса исключения, если курс не найден.
     */
    public function testGetRateDataThrowsExceptionIfCurrencyRateNotFound(): void
    {
        $date = '2024-09-25';
        $currencyCode = 'USD';
        $baseCurrency = 'RUB';

        // Настраиваем мок репозитория для возврата пустого массива для обеих валют
        $this->repositoryMock
            ->method('getCurrencyRate')
            ->willReturnOnConsecutiveCalls(
                ['rate' => null, 'actual_date' => $date], // Для валюты
                ['rate' => null, 'actual_date' => $date]  // Для базовой валюты
            );

        // Ожидаем выброса исключения
        $this->expectException(\Exception::class);

        // Выполняем метод
        $this->provider->getRateData($date, $currencyCode, $baseCurrency);
    }

    /**
     * Тест успешного получения курса за предыдущий торговый день.
     */
    public function testGetPreviousRateSuccess(): void
    {
        $currentActualDate = '2024-09-25';
        $previousDate = '2024-09-24';
        $currencyCode = 'USD';
        $baseCurrency = 'RUB';

        // Настраиваем мок репозитория для возврата курсов валют
        $this->repositoryMock
            ->method('getCurrencyRate')
            ->willReturnMap([
                [$previousDate, $currencyCode, ['rate' => 74.0, 'actual_date' => $previousDate]],
                [$previousDate, $baseCurrency, ['rate' => 1.0, 'actual_date' => $previousDate]]
            ]);

        // Выполняем метод
        $result = $this->provider->getPreviousRate($currentActualDate, $currencyCode, $baseCurrency);

        // Проверка результата
        $this->assertInstanceOf(CurrencyRateDTO::class, $result);
        $this->assertEquals(74.0, $result->rate);
        $this->assertEquals($previousDate, $result->actualDate);
    }

    /**
     * Тест проверки уменьшения даты, если предыдущий курс не найден сразу.
     */
    public function testGetPreviousRateIteratesToFindValidRate(): void
    {
        $currentActualDate = '2024-09-25';
        $previousDate1 = '2024-09-24';
        $previousDate2 = '2024-09-23';
        $currencyCode = 'USD';
        $baseCurrency = 'RUB';

        // Настраиваем мок репозитория для возврата пустых данных для первой даты и валидных курсов для второй даты
        $this->repositoryMock
            ->method('getCurrencyRate')
            ->willReturnMap([
                [$previousDate1, $currencyCode, ['rate' => null, 'actual_date' => $previousDate1]],
                [$previousDate1, $baseCurrency, ['rate' => null, 'actual_date' => $previousDate1]],
                [$previousDate2, $currencyCode, ['rate' => 73.0, 'actual_date' => $previousDate2]],
                [$previousDate2, $baseCurrency, ['rate' => 1.0, 'actual_date' => $previousDate2]]
            ]);

        // Выполняем метод
        $result = $this->provider->getPreviousRate($currentActualDate, $currencyCode, $baseCurrency);

        // Проверка результата
        $this->assertInstanceOf(CurrencyRateDTO::class, $result);
        $this->assertEquals(73.0, $result->rate);
        $this->assertEquals($previousDate2, $result->actualDate);
    }
}
