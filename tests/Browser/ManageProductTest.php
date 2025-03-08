<?php

namespace Tests\Browser;

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
        $this->loginDuskUser('admin');
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
            $browser->click('.dt-auto-Product-1 .table-wrapper .header .button-new-entry')->waitFor("@product-name", seconds: self::maxWaitInSeconds);

            $browser->type('@product-name', 'Dusk-Test-'.date(SystemService::dateIsoFormat8601));
            $browser->select('payment_method_id'); // null = select random
            $browser->select('shipping_method_id'); // null = select random
            $browser->screenshot('manage_product_2');
            // $browser->script("document.getElementsByClassName('form-actions')[0].scrollIntoView()");

            // there are at least 2 buttons (media dependent)
            $this->clickFormActionAndWaitFor($browser, 'product-form', '.form-action-accept');
            $browser->waitForTextIn('.form-container .messages', __(":name updated.", ['name' => __('Product')]), self::maxWaitInSeconds);
            $browser->screenshot('manage_product_4');
        });
    }
}
