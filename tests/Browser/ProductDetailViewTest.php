<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Modules\Market\app\Models\Product;
use Tests\DuskTestCase;

class ProductDetailViewTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testLogin(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')->assertSee(__('Login'));
            $browser->screenshot('login before');
            $browser->type('email', 'local-js-dummy-a-0001@local.test')
                ->type('password', '1234567')
                ->press(__('Login'))
                ->waitForRoute('home')
                // ->assertRouteIs('home');
                ->assertSee(__('Cart'));
            $browser->screenshot('login after');
        });
    }

    /**
     * A Dusk test example.
     */
    public function testProductDetailView(): void
    {
        $this->browse(function (Browser $browser) {
            $productWebUri = 'neque_product_6421b065e0d5a';
            $browser->visit('/')->assertSee(__('Cart'));
            $browser->screenshot('product_detail_view_1');
            $browser->click('#main-nav-29')->assertVisible('#main-nav-cat-17');
            $browser->click('#main-nav-cat-17')->waitForRoute('category-products', ['category' => 'category-0017']);
            $browser->screenshot('product_detail_view_2');
            $browser->click('a[href*="product/'.$productWebUri.'"]')
                ->waitForRoute('product', ['product' => $productWebUri]);
            $browser->assertSee(__("Detailed Item Information"));
            $browser->screenshot('product_detail_view_3');
        });
    }

    /**
     * A Dusk test example.
     */
    public function testProductToCart(): void
    {
        $this->browse(function (Browser $browser) {

            $productWebUri = 'neque_product_6421b065e0d5a';
            $selectorCartButtonAdd = '.product-box .btn-add-to-cart';
            $selectorCartButtonRemove = '.product-box .btn-remove-from-cart';
            $selectorCartBadge = '.main-nav-bar .mini-cart .badge';


            if ($product = Product::with([])->where('web_uri', $productWebUri)->first()) {

                // // remove product from cart if exist
                // $cart = app('market_settings')->getCurrentShoppingCart();
                // $cart->removeProduct($product->getKey());

                $browser->screenshot('testProductToCart_1');

                $browser->visitRoute('product', ['product' => $productWebUri])
                    ->assertSee(__("Detailed Item Information"));

                $browser->pause(2000);
                // if ($e = $browser->element($selectorCartBadge.'[data-cart-loaded="1"]')) {
                // $browser->screenshot('testProductToCart_2');
                $cartBadgeElement = $browser->element($selectorCartBadge);
                $count = $cartBadgeElement->getText();
                // Log::debug("Cart Count: ", [$count]);

                $isInCartAtStart = false;
                if ($e = $browser->element($selectorCartButtonRemove)) {
                    $isInCartAtStart = true;
                    // Log::debug("cart button: ", [$e->getText()]);
                } else {
                    if ($e = $browser->element($selectorCartButtonAdd)) {
                        // Log::debug("cart button: ", [$e->getText()]);
                    }
                }

                $isInCart = $isInCartAtStart;
                if ($e) {

                    // Add or remove from cart ...
                    $e->click();

                    if ($isInCart) {
                        $browser->waitFor($selectorCartButtonAdd);
                    } else {
                        $browser->waitFor($selectorCartButtonRemove);
                    }
                    $browser->screenshot('testProductToCart_3');

                }


                // $browser->click("Add to Cart");
                // $browser->click("Remove from Cart");


                // $browser->wait
            } else {
                $this->fail('Product not found.');
            }

        });
    }
}
