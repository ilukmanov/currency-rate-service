<?php

namespace app\provider;

use Exception;
use GuzzleHttp\Client;

/**
 * Провайдер для получения данных о курсах валют с ЦБР.
 */
class CbrProvider
{
    /** @var Client HTTP-клиент. */
    protected Client $client;

    /** @var string URL для API ЦБР. */
    protected string $cbrUrl;

    public function __construct(?string $cbrUrl = null, ?Client $client = null)
    {
        $this->client = $client ?? new Client();
        $this->cbrUrl = $cbrUrl ?? config('cbr.cbr_url');

        if ($this->cbrUrl === null) {
            throw new \InvalidArgumentException("URL для API ЦБР не может быть null");
        }
    }

    /**
     * Получить курсы валют с ЦБР.
     *
     * @param string $date Дата курса.
     *
     * @return array Массив с курсами.
     */
    public function fetchCurrencyRates(string $date): ?array
    {
        // Преобразуем дату в нужный формат для запроса
        $formattedDate = date('d/m/Y', strtotime($date));

        // Выполняем запрос к ЦБР
        $response = $this->client->request('GET', $this->cbrUrl, [
            'query' => ['date_req' => $formattedDate]
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new Exception("Ошибка при загрузке курса валюты с cbr.ru");
        }

        // Парсим XML-ответ
        $xml = simplexml_load_string($response->getBody()->getContents());

        // Получаем фактическую дату торгов
        $actualTradingDate = date('Y-m-d', strtotime((string) $xml['Date']));
        $rates = [];

        foreach ($xml->Valute as $valute) {
            $currencyCode = (string) $valute->CharCode;
            $rate = (float) str_replace(',', '.', (string) $valute->Value);
            $rates[$currencyCode] = $rate;
        }

        return [
            'date' => $actualTradingDate,
            'rates' => $rates,
        ];
    }
}
