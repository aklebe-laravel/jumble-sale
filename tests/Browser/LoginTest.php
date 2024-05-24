<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')->assertSee(__('Login'));
            $browser->type('email', 'local-js-dummy-a-0001@local.test')
                ->type('password', '1234567')
                ->press(__('Login'))
                ->waitForRoute('home')
                // ->assertRouteIs('home');
            ->assertSee(__('Cart'));
            $browser->screenshot('logged_in');
        });
    }
}
