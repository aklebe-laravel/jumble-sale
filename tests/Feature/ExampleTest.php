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
        $publicSite = app('website_base_config')->getValue('site.public');
        $response->assertStatus($publicSite ? 200 : 302);
    }
}
