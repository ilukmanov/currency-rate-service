<?php

namespace app\exception;

use Throwable;
use Tracy\Debugger;
use Webman\Exception\ExceptionHandler;
use Webman\Http\Request;
use Webman\Http\Response;

/**
 * Класс Handler
 *
 * Кастомный обработчик исключений для фреймворка Webman, который интегрирует Tracy для логирования и отображения ошибок.
 *
 * @package app\exception
 */
class Handler extends ExceptionHandler
{
    /**
     * Список типов исключений, которые не должны быть записаны в лог.
     *
     * @var array
     */
    public $dontReport = [
        BusinessException::class,
    ];

    /**
     * Сообщить или записать в лог информацию об исключении.
     *
     * Этот метод записывает исключение с использованием Tracy, если Tracy включён, а затем вызывает
     * родительский метод report.
     *
     * @param Throwable $exception Исключение.
     * @return void
     */
    public function report(Throwable $exception)
    {
        if (Debugger::isEnabled()) {
            Debugger::log($exception, Debugger::EXCEPTION);
        }
        parent::report($exception);
    }

    /**
     * Преобразовать исключение в HTTP-ответ.
     *
     * Этот метод проверяет, является ли исключение экземпляром BusinessException и может ли оно само
     * отобразить ответ. В противном случае используется Tracy для отображения подробной страницы ошибки, если Tracy включён,
     * или вызывается родительский метод render.
     *
     * @param Request $request HTTP-запрос.
     * @param Throwable $exception Исключение.
     * @return Response HTTP-ответ.
     */
    public function render(Request $request, Throwable $exception): Response
    {
        if (($exception instanceof BusinessException) && ($response = $exception->render($request))) {
            return $response;
        }

        if (Debugger::isEnabled()) {
            ob_start();
            Debugger::getBlueScreen()->render($exception);
            $content = ob_get_clean();
            return new Response(500, ['Content-Type' => 'text/html'], $content);
        }

        return parent::render($request, $exception);
    }
}
