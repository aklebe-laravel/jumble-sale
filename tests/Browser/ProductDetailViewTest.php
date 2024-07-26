<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Modules\Market\app\Models\Product;
use Tests\DuskTestCase;

class ProductDetailViewTest extends DuskTestCase
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
     * Show product detail view
     */
    public function testProductDetailView(): void
    {
        $this->browse(function (Browser $browser) {
            $productWebUri = 'neque_product_6421b065e0d5a';
            $browser->visit('/')->assertSee(__('Cart'));
            $browser->screenshot('product_detail_view_1');
            $browser->click('#main-nav-30')->assertVisible('#main-nav-cat-17');
            $browser->click('#main-nav-cat-17')->waitForRoute('category-products', ['category' => 'category-0017'], seconds: self::maxWaitInSeconds);
            $browser->screenshot('product_detail_view_2');
            $browser->click('a[href*="product/'.$productWebUri.'"]')
                ->waitForRoute('product', ['product' => $productWebUri], seconds: self::maxWaitInSeconds);
            $browser->assertSee(__("Detailed Item Information"));
            $browser->screenshot('product_detail_view_3');
        });
    }

    /**
     * Add product to cart
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
                        $browser->waitFor($selectorCartButtonAdd, seconds: self::maxWaitInSeconds);
                    } else {
                        $browser->waitFor($selectorCartButtonRemove, seconds: self::maxWaitInSeconds);
                    }
                    $browser->screenshot('testProductToCart_3');

                }


                // $browser->click("Add to Cart");
                // $browser->click("Remove from Cart");

            } else {
                $this->fail('Product not found.');
            }

        });
    }

}
