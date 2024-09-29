<?php

namespace tests\provider;

use PHPUnit\Framework\TestCase;
use app\provider\FixedRateProvider;

/**
 * Тесты для FixedRateProvider.
 */
class FixedRateProviderTest extends TestCase
{
    /** @var FixedRateProvider */
    private FixedRateProvider $fixedRateProvider;

    protected function setUp(): void
    {
        // Создаем экземпляр FixedRateProvider
        $this->fixedRateProvider = new FixedRateProvider();
    }

    /**
     * Тест, который проверяет, что возвращается фиксированный курс для RUR.
     */
    public function testGetRateReturnsOneForRUR(): void
    {
        $currencyCode = 'RUR';
        $rate = $this->fixedRateProvider->getRate($currencyCode);

        // Проверка, что курс для RUR равен 1.0
        $this->assertEquals(1.0, $rate);
    }

    /**
     * Тест, который проверяет, что для любой другой валюты возвращается null.
     */
    public function testGetRateReturnsNullForOtherCurrencies(): void
    {
        $currencyCode = 'USD';
        $rate = $this->fixedRateProvider->getRate($currencyCode);

        // Проверка, что курс для валюты, отличной от RUR, равен null
        $this->assertNull($rate);
    }

    /**
     * Тест, который проверяет, что метод нечувствителен к регистру валютного кода.
     */
    public function testGetRateIsCaseInsensitive(): void
    {
        $currencyCode = 'rur';
        $rate = $this->fixedRateProvider->getRate($currencyCode);

        // Проверка, что курс для RUR равен 1.0, независимо от регистра
        $this->assertEquals(1.0, $rate);
    }
}
