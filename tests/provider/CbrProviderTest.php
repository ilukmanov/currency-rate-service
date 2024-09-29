<?php

namespace tests\provider;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use app\provider\CbrProvider;

/**
 * Тесты для CbrProvider.
 */
class CbrProviderTest extends TestCase
{
    /** @var string URL для API ЦБР. */
    private string $cbrUrl = 'http://example.com';

    /**
     * Тест успешного получения курсов валют.
     *
     * @return void
     */
    public function testFetchCurrencyRatesSuccess(): void
    {
        // Создаем мок ответа с корректным XML
        $xmlContent = <<<XML
<ValCurs Date="24.09.2024" name="Foreign Currency Market">
    <Valute>
        <CharCode>USD</CharCode>
        <Value>75,50</Value>
    </Valute>
    <Valute>
        <CharCode>EUR</CharCode>
        <Value>80,75</Value>
    </Valute>
</ValCurs>
XML;
        $mock = new MockHandler([
            new Response(200, [], $xmlContent)
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Создаем экземпляр CbrProvider с мокнутым клиентом и URL
        $cbrProvider = new CbrProvider($this->cbrUrl, $client);

        // Выполнение метода
        $date = '2024-09-24';
        $result = $cbrProvider->fetchCurrencyRates($date);

        // Проверка результата
        $this->assertEquals('2024-09-24', $result['date']);
        $this->assertArrayHasKey('USD', $result['rates']);
        $this->assertArrayHasKey('EUR', $result['rates']);
        $this->assertEquals(75.50, $result['rates']['USD']);
        $this->assertEquals(80.75, $result['rates']['EUR']);
    }

    /**
     * Тест выброса исключения при ошибке HTTP-запроса.
     *
     * @return void
     */
    public function testFetchCurrencyRatesThrowsExceptionOnHttpError(): void
    {
        // Создаем мок ответа с кодом 500
        $mock = new MockHandler([
            new RequestException("Ошибка при загрузке курса валюты с cbr.ru", new Request('GET', $this->cbrUrl))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Создаем экземпляр CbrProvider с мокнутым клиентом и URL
        $cbrProvider = new CbrProvider($this->cbrUrl, $client);

        // Проверка, что метод выбрасывает исключение
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Ошибка при загрузке курса валюты с cbr.ru');

        // Выполнение метода
        $date = '2024-09-24';
        $cbrProvider->fetchCurrencyRates($date);
    }

    /**
     * Тест успешного парсинга ответа с одной валютой.
     *
     * @return void
     */
    public function testFetchCurrencyRatesWithSingleCurrency(): void
    {
        // Создаем мок ответа с одной валютой
        $xmlContent = <<<XML
<ValCurs Date="24.09.2024" name="Foreign Currency Market">
    <Valute>
        <CharCode>USD</CharCode>
        <Value>75,50</Value>
    </Valute>
</ValCurs>
XML;
        $mock = new MockHandler([
            new Response(200, [], $xmlContent)
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Создаем экземпляр CbrProvider с мокнутым клиентом и URL
        $cbrProvider = new CbrProvider($this->cbrUrl, $client);

        // Выполнение метода
        $date = '2024-09-24';
        $result = $cbrProvider->fetchCurrencyRates($date);

        // Проверка результата
        $this->assertEquals('2024-09-24', $result['date']);
        $this->assertArrayHasKey('USD', $result['rates']);
        $this->assertEquals(75.50, $result['rates']['USD']);
    }
}
