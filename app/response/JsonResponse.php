<?php

namespace app\response;

use support\Response;

class JsonResponse
{
    /**
     * Добавляет JSON_PRETTY_PRINT в параметры, если включён режим отладки.
     *
     * @param int $options Параметры кодирования JSON.
     * @return int Изменённые параметры кодирования JSON.
     */
    private static function applyDebugOptions(int $options): int
    {
        if (config('app.debug') == 'true') {
            $options += JSON_PRETTY_PRINT;
        }
        return $options;
    }

    /**
     * Возвращает успешный ответ.
     *
     * @param mixed $data Данные, которые включить в ответ.
     * @param int $status HTTP статус-код.
     * @param int $options Параметры кодирования JSON.
     * @return Response Успешный ответ.
     */
    public static function success(mixed $data, int $status = 200, int $options = JSON_UNESCAPED_UNICODE): Response
    {
        $result = [
            "status" => "success",
            "data" => $data,
            "errors" => null,
        ];
        $options = self::applyDebugOptions($options);
        return new Response($status, ['Content-Type' => 'application/json'], json_encode($result, $options));
    }

    /**
     * Возвращает предупреждающий ответ.
     *
     * @param mixed $errors Ошибки, которые включить в ответ.
     * @param int $status HTTP статус-код.
     * @param int $options Параметры кодирования JSON.
     * @return Response Предупреждающий ответ.
     */
    public static function warning(mixed $errors, int $status = 400, int $options = JSON_UNESCAPED_UNICODE): Response
    {
        $result = [
            "status" => "warning",
            "data" => null,
            "errors" => $errors,
        ];
        $options = self::applyDebugOptions($options);
        return new Response($status, ['Content-Type' => 'application/json'], json_encode($result, $options));
    }

    /**
     * Возвращает ответ с ошибкой.
     *
     * @param mixed $errors Ошибки, которые включить в ответ.
     * @param int $status HTTP статус-код.
     * @param int $options Параметры кодирования JSON.
     * @return Response Ответ с ошибкой.
     */
    public static function error(mixed $errors, int $status = 500, int $options = JSON_UNESCAPED_UNICODE): Response
    {
        $result = [
            "status" => "error",
            "data" => null,
            "errors" => $errors,
        ];
        $options = self::applyDebugOptions($options);
        return new Response($status, ['Content-Type' => 'application/json'], json_encode($result, $options));
    }
}
