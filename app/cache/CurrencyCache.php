<?php

namespace app\cache;

use Exception;

/**
 * Кэширует курсы валют в памяти и в файловой системе.
 * Класс реализует двухуровневый кэш.
 * Холодный кэш хранится в файлах.
 * Горячий хэш хранится в памяти, в статическом массиве.
 * Это возможно благодаря тому, что мы используем application сервис Workerman под капотом.
 * Можно хранить в Redis, но такой способ - намного производительнее.
 */
class CurrencyCache
{
    /** @var array $inMemoryCache Кэш в памяти. */
    protected static array $inMemoryCache = [];

    /** @var string Путь к папке для хранения файлового кэша */
    protected static string $cacheDir = __DIR__ . '/../../runtime/cache/currency';

    /**
     * Кэшировать курсы за указанную дату.
     *
     * @param string $date Дата в формате 'Y-m-d'.
     * @param array $data Курсы валют и фактическая дата.
     */
    public static function cacheRates(string $date, array $data): void
    {
        // Кэшируем в память
        self::$inMemoryCache[$date] = $data;

        // Кэшируем в файл
        $filePath = self::getFilePath($date);
        self::ensureDirectoryExists(dirname($filePath));
        file_put_contents($filePath, json_encode($data));
    }

    /**
     * Получить курсы за дату из кэша.
     *
     * @param string $date Дата в формате 'Y-m-d'.
     * @return array|null Курсы и фактическая дата или null, если не найдены.
     */
    public static function getRates(string $date): ?array
    {
        // Проверяем кэш в памяти
        if (isset(self::$inMemoryCache[$date])) {
            return self::$inMemoryCache[$date];
        }

        // Пытаемся загрузить из файлового кэша
        $filePath = self::getFilePath($date);
        if (file_exists($filePath)) {
            $data = json_decode(file_get_contents($filePath), true);
            // Кэшируем в память после загрузки из файла
            self::$inMemoryCache[$date] = $data;
            return $data;
        }

        return null;
    }

    /**
     * Получить путь к файлу кэша для указанной даты.
     *
     * @param string $date Дата в формате 'Y-m-d'.
     * @return string Путь к файлу.
     */
    protected static function getFilePath(string $date): string
    {
        $parts = explode('-', $date);
        if (count($parts) !== 3) {
            throw new \InvalidArgumentException("Некорректный формат даты: $date. Ожидается формат Y-m-d.");
        }

        [$year, $month, $day] = $parts;
        return self::$cacheDir . "/$year/$month/$day.json";
    }

    /**
     * Убедиться, что директория существует.
     *
     * @param string $directory Путь к директории.
     */
    protected static function ensureDirectoryExists(string $directory): void
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }
}
