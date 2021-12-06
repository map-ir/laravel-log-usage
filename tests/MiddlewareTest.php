<?php

namespace MapIr\LaravelLogUsage\Tests;

use Illuminate\Http\Request;
use MapIr\LaravelLogUsage\Http\Middleware\LogUsageMiddleware;
use Orchestra\Testbench\TestCase;
use MapIr\LaravelLogUsage\LaravelLogUsageServiceProvider;

class MiddlewareTest extends TestCase
{

    protected function getPackageProviders($app): array
    {
        return [LaravelLogUsageServiceProvider::class];
    }

    /** @test */
    public function MiddlewareTest()
    {
        // Given we have a request
        $request = new Request();
        // when we pass the request to this middleware,
        $response = (new LogUsageMiddleware())->handle($request, function ($request) { });
        $this->assertIsArray($response);
    }
}
