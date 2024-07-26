<?php

namespace Browser;

use Laravel\Dusk\Browser;
use Modules\SystemBase\app\Services\SystemService;
use Tests\DuskTestCase;

class ManageProductTest extends DuskTestCase
{
    /**
     * Login user
     */
    public function testLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')->assertSee(__('Login'));
            $browser->screenshot('login before');
            $browser->type('email', 'AdminTest2@local.test')
                ->type('password', '1234567')
                ->press(__('Login'))
                ->waitForRoute('home', seconds: self::maxWaitInSeconds)
                // ->assertRouteIs('home');
                ->assertSee(__('Cart'));
            $browser->screenshot('login after');
        });
    }

    /**
     * Add product to cart
     */
    public function testAddProduct(): void
    {
        $this->browse(function (Browser $browser) {

            // navigate to manage products
            $browser->visit('/manage-data/Product')->assertSee(__('Cart'));
            $browser->screenshot('manage_product_1');
            $browser->click('.dt-auto-Product-1 .table-wrapper .header #dt-header-setting-1-actions')->assertVisible('.dt-auto-Product-1 .table-wrapper .header .button-new-entry');
            // $browser->click('.dt-auto-Product-1 .table-wrapper .header .button-new-entry')->waitFor(".dt-edit-form input[name='name']", seconds: self::maxWaitInSeconds);
            $browser->click('.dt-auto-Product-1 .table-wrapper .header .button-new-entry')->waitFor("@product-name", seconds: self::maxWaitInSeconds);

            $browser->type('@product-name', 'Dusk-Test-'.date(SystemService::dateIsoFormat8601));
            $browser->select('payment_method_id'); // null = select random
            $browser->select('shipping_method_id'); // null = select random
            $browser->screenshot('manage_product_2');
            // $browser->script("document.getElementsByClassName('form-actions')[0].scrollIntoView()");
            $acceptButton = '.dt-edit-form .form-actions .form-action-accept';
            $browser->scrollTo($acceptButton)->waitFor($acceptButton);
            $browser->screenshot('manage_product_3');
            $browser->click($acceptButton)->waitForTextIn('.messages', __("Data saved successfully."));
        });
    }
}
