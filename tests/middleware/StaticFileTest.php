<?php

namespace tests\middleware;

use PHPUnit\Framework\TestCase;
use Mockery as m;
use Webman\Http\Request;
use Webman\Http\Response;
use app\middleware\StaticFile;

/**
 * Тесты для класса StaticFile.
 */
class StaticFileTest extends TestCase
{
    /**
     * Очистка окружения теста.
     */
    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }

    /**
     * Тест метода process для блокировки доступа к файлам, начинающимся с точки.
     */
    public function testProcessBlocksDotFiles()
    {
        /** @var Request|m\MockInterface $request */
        $request = m::mock(Request::class);
        $request->shouldReceive('path')->andReturn('/.hiddenfile');

        $middleware = new StaticFile();
        $next = function (Request $req) {
            return new Response(200);
        };

        $response = $middleware->process($request, $next);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals('<h1>403 forbidden</h1>', $response->rawBody());
    }

    /**
     * Тест метода process для разрешения доступа к обычным файлам.
     */
    public function testProcessAllowsNormalFiles()
    {
        /** @var Request|m\MockInterface $request */
        $request = m::mock(Request::class);
        $request->shouldReceive('path')->andReturn('/normalfile');

        $expectedResponse = new Response(200);

        $middleware = new StaticFile();
        $next = function (Request $req) use ($expectedResponse) {
            return $expectedResponse;
        };

        $response = $middleware->process($request, $next);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * Тест метода process для добавления CORS заголовков.
     */
    public function testProcessAddsCorsHeaders()
    {
        /** @var Request|m\MockInterface $request */
        $request = m::mock(Request::class);
        $request->shouldReceive('path')->andReturn('/normalfile');

        $initialResponse = new Response(200);
        $initialResponse->withHeader('Access-Control-Allow-Origin', '*');
        $initialResponse->withHeader('Access-Control-Allow-Credentials', 'true');

        $middleware = new StaticFile();
        $next = function (Request $req) use ($initialResponse) {
            return $initialResponse;
        };

        $response = $middleware->process($request, $next);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('*', $response->getHeader('Access-Control-Allow-Origin'));
        $this->assertEquals('true', $response->getHeader('Access-Control-Allow-Credentials'));
    }
}
