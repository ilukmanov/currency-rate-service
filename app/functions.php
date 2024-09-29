<?php

use app\response\JsonResponse;
use support\Response;
use Tracy\Debugger;

if (config('app.debug') == true) {
    Debugger::enable(Debugger::Development, base_path() . '/runtime/logs/exception');
    Debugger::$showBar = true;
    Debugger::$strictMode = true;
}

/**
 * Возвращает успешный ответ.
 *
 * @param mixed $data Данные, которые включить в ответ.
 * @param int $status HTTP статус-код.
 * @param int $options Параметры кодирования JSON.
 * @return Response Успешный ответ.
 */
function success(mixed $data, int $status = 200, int $options = JSON_UNESCAPED_UNICODE): Response
{
    return JsonResponse::success($data, $status, $options);
}

/**
 * Возвращает предупреждающий ответ.
 *
 * @param mixed $errors Ошибки, которые включить в ответ.
 * @param int $status HTTP статус-код.
 * @param int $options Параметры кодирования JSON.
 * @return Response Предупреждающий ответ.
 */
function warning(mixed $errors, int $status = 400, int $options = JSON_UNESCAPED_UNICODE): Response
{
    return JsonResponse::warning($errors, $status, $options);
}

/**
 * Возвращает ответ с ошибкой.
 *
 * @param mixed $errors Ошибки, которые включить в ответ.
 * @param int $status HTTP статус-код.
 * @param int $options Параметры кодирования JSON.
 * @return Response Ответ с ошибкой.
 */
function error(mixed $errors, int $status = 500, int $options = JSON_UNESCAPED_UNICODE): Response
{
    return JsonResponse::error($errors, $status, $options);
}
