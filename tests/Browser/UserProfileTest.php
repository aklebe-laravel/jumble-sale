<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UserProfileTest extends DuskTestCase
{
    /**
     * Login user
     */
    public function testLogin(): void
    {
        $this->loginAdmin();
    }

    /**
     *
     */
    public function testUserProfile(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')->assertVisible('#navbarHeaderProfile');

            // click/open dropdown,
            $browser->click('#navbarHeaderProfile')->assertSee(__('UserProfile'));
            // and click the user profile ...
            $browser->click('@user-profile')->waitForRoute('user-profile', seconds: self::maxWaitInSeconds);
            // product listing appears ...
            $browser->screenshot('user_profile_view_1');

        });
    }

}
