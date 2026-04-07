<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;

abstract class TestCase extends BaseTestCase
{
    use InteractsWithSession;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Initialize session and CSRF token for stateful requests
        $this->startSession();
        $this->withHeader('X-CSRF-TOKEN', csrf_token());
    }

    /**
     * Make a POST request with automatic CSRF token handling.
     *
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Testing\TestResponse
     */
    public function post($uri, array $data = [], array $headers = [])
    {
        // Ensure session is started and CSRF token is current
        if (!$this->app['session']->isStarted()) {
            $this->startSession();
        }
        $this->withHeader('X-CSRF-TOKEN', csrf_token());
        
        return parent::post($uri, $data, $headers);
    }

    /**
     * Make a PUT request with automatic CSRF token handling.
     *
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Testing\TestResponse
     */
    public function put($uri, array $data = [], array $headers = [])
    {
        // Ensure session is started and CSRF token is current
        if (!$this->app['session']->isStarted()) {
            $this->startSession();
        }
        $this->withHeader('X-CSRF-TOKEN', csrf_token());
        
        return parent::put($uri, $data, $headers);
    }

    /**
     * Make a PATCH request with automatic CSRF token handling.
     *
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Testing\TestResponse
     */
    public function patch($uri, array $data = [], array $headers = [])
    {
        // Ensure session is started and CSRF token is current
        if (!$this->app['session']->isStarted()) {
            $this->startSession();
        }
        $this->withHeader('X-CSRF-TOKEN', csrf_token());
        
        return parent::patch($uri, $data, $headers);
    }

    /**
     * Make a DELETE request with automatic CSRF token handling.
     *
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Testing\TestResponse
     */
    public function delete($uri, array $data = [], array $headers = [])
    {
        // Ensure session is started and CSRF token is current
        if (!$this->app['session']->isStarted()) {
            $this->startSession();
        }
        $this->withHeader('X-CSRF-TOKEN', csrf_token());
        
        return parent::delete($uri, $data, $headers);
    }
}
