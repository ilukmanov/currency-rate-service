<?php

namespace tests\repository;

use PHPUnit\Framework\TestCase;
use app\repository\CurrencyRepository;
use app\provider\FixedRateProvider;
use app\provider\CbrProvider;
use app\cache\CurrencyCache;

/**
 * Тесты для CurrencyRepository.
 */
class CurrencyRepositoryTest extends TestCase
{
    /** @var FixedRateProvider&\PHPUnit\Framework\MockObject\MockObject */
    private $fixedRateProviderMock;

    /** @var CbrProvider&\PHPUnit\Framework\MockObject\MockObject */
    private $cbrProviderMock;

    /** @var CurrencyRepository */
    private CurrencyRepository $repository;

    protected function setUp(): void
    {
        // Создаем мок провайдеров
        $this->fixedRateProviderMock = $this->createMock(FixedRateProvider::class);
        $this->cbrProviderMock = $this->createMock(CbrProvider::class);

        // Создаем экземпляр CurrencyRepository с моками
        $this->repository = new CurrencyRepository($this->fixedRateProviderMock, $this->cbrProviderMock);
    }

    /**
     * Тест, который проверяет получение фиксированного курса.
     */
    public function testGetCurrencyRateReturnsFixedRate(): void
    {
        $date = '2024-09-25';
        $currencyCode = 'RUR';

        // Настраиваем мок фиксированного провайдера, чтобы он возвращал курс 1.0
        $this->fixedRateProviderMock
            ->method('getRate')
            ->with($currencyCode)
            ->willReturn(1.0);

        // Выполняем метод
        $result = $this->repository->getCurrencyRate($date, $currencyCode);

        // Проверяем, что результат содержит правильные данные
        $this->assertEquals(1.0, $result['rate']);
        $this->assertEquals($date, $result['actual_date']);
    }

    /**
     * Тест, который проверяет получение курса из кэша.
     */
    public function testGetCurrencyRateReturnsRateFromCache(): void
    {
        $date = '2024-09-25';
        $currencyCode = 'USD';
        $cachedData = [
            'rates' => [$currencyCode => 75.5],
            'actual_date' => $date
        ];

        // Очистка кэша перед тестом
        CurrencyCache::cacheRates($date, []);

        // Мокаем кэширование, чтобы вернуть заранее определенные данные
        CurrencyCache::cacheRates($date, $cachedData);

        // Выполняем метод
        $result = $this->repository->getCurrencyRate($date, $currencyCode);

        // Проверяем, что результат содержит данные из кэша
        $this->assertEquals(75.5, $result['rate']);
        $this->assertEquals($date, $result['actual_date']);
    }

    /**
     * Тест, который проверяет получение курса из внешнего источника, если данных в кэше нет.
     */
    public function testGetCurrencyRateReturnsRateFromExternalSource(): void
    {
        $date = '2024-09-25';
        $currencyCode = 'USD';
        $fetchedData = [
            'date' => $date,
            'rates' => [$currencyCode => 76.0]
        ];

        // Очистка кэша перед тестом
        CurrencyCache::cacheRates($date, []);

        // Настраиваем мок фиксированного провайдера, чтобы он возвращал null
        $this->fixedRateProviderMock
            ->method('getRate')
            ->with($currencyCode)
            ->willReturn(null);

        // Настраиваем мок CBR провайдера, чтобы он возвращал данные
        $this->cbrProviderMock
            ->method('fetchCurrencyRates')
            ->with($date)
            ->willReturn($fetchedData);

        // Выполняем метод
        $result = $this->repository->getCurrencyRate($date, $currencyCode);

        // Проверяем, что результат содержит правильные данные из внешнего источника
        $this->assertEquals(76.0, $result['rate']);
        $this->assertEquals($date, $result['actual_date']);
    }
}
