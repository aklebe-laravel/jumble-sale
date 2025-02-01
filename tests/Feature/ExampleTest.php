<?php

namespace Tests\Feature;

use Modules\SystemBase\tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get('/');
        $response->assertStatus(config('website-base.module_website_public', false) ? 200 : 302);
    }
}
