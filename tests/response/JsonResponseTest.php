<?php

namespace tests\response;

use PHPUnit\Framework\TestCase;
use app\response\JsonResponse;
use support\Response;

/**
 * Тесты для класса JsonResponse.
 */
class JsonResponseTest extends TestCase
{
    /**
     * Тест успешного ответа.
     */
    public function testSuccessResponse(): void
    {
        $data = ['message' => 'Operation successful'];
        $response = JsonResponse::success($data);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeader('Content-Type'));
        $expectedBody = json_encode([
            "status" => "success",
            "data" => $data,
            "errors" => null,
        ], JSON_UNESCAPED_UNICODE);
        $this->assertEquals($expectedBody, (string)$response->rawBody());
    }

    /**
     * Тест предупреждающего ответа.
     */
    public function testWarningResponse(): void
    {
        $errors = ['message' => 'There was a warning'];
        $response = JsonResponse::warning($errors, 400);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeader('Content-Type'));
        $expectedBody = json_encode([
            "status" => "warning",
            "data" => null,
            "errors" => $errors,
        ], JSON_UNESCAPED_UNICODE);
        $this->assertEquals($expectedBody, (string)$response->rawBody());
    }

    /**
     * Тест ответа с ошибкой.
     */
    public function testErrorResponse(): void
    {
        $errors = ['message' => 'An error occurred'];
        $response = JsonResponse::error($errors, 500);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeader('Content-Type'));
        $expectedBody = json_encode([
            "status" => "error",
            "data" => null,
            "errors" => $errors,
        ], JSON_UNESCAPED_UNICODE);
        $this->assertEquals($expectedBody, (string)$response->rawBody());
    }

    /**
     * Тест применения параметра JSON_PRETTY_PRINT при включенном режиме отладки.
     */
    public function testDebugModeAppliesPrettyPrint(): void
    {
        // Устанавливаем режим отладки в true
        putenv('app.debug=true');

        $data = ['message' => 'Operation successful'];
        $response = JsonResponse::success($data, 200);

        $expectedBody = json_encode([
            "status" => "success",
            "data" => $data,
            "errors" => null,
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $actualBody = (string)$response->rawBody();

        $this->assertEquals(json_decode($expectedBody, true), json_decode($actualBody, true));

        // Сбрасываем режим отладки
        putenv('app.debug=false');
    }
}
