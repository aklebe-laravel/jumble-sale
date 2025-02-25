<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Modules\Market\app\Models\Category;
use Modules\Market\app\Models\Product;
use Modules\WebsiteBase\app\Models\Navigation;
use Tests\DuskTestCase;

class ProductDetailViewTest extends DuskTestCase
{
    /**
     * Login user
     */
    public function testLogin(): void
    {
        $this->loginAdmin();
    }

    /**
     * Show product detail view
     *
     * 1) Click a category from navigation
     * 2) Click a product in the category products list
     * 3) Check the product detail view
     *
     */
    public function testProductDetailView(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')->assertSee(__('Cart'));
            $browser->screenshot('product_detail_view_1');

            // 1) Get category navigation item from root navigation ...
            /** @var Navigation $navRoot */
            $navRoot = Navigation::with([])->where('code', 'Categories-Menu-L1')->first();
            $this->assertFalse(!$navRoot);

            // get the first category ...
            /** @var Category $category */
            $category = Category::with([])->frontendItems()->inRandomOrder()->first();
            $this->assertFalse(!$category);
            $navCategoryElSelector = '#main-nav-cat-'.$category->getKey();
            // click/open dropdown, find the specific category nav item ...
            $browser->click('#main-nav-'.$navRoot->getKey())->assertVisible($navCategoryElSelector);
            // and click the category ...
            $browser->click($navCategoryElSelector)->waitForRoute('category-products', ['category' => $category->web_uri], seconds: self::maxWaitInSeconds);
            // product listing appears ...
            $browser->screenshot('product_detail_view_2');

            // 2) click a product in the list
            // 3) Check the product details view
            $browser->click('.product-list .product .item .title')->assertVisible('.product-box .info-container h2');
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

            if ($product = Product::with([])->frontendItems()->first()) {

                // // remove product from cart if exist
                // $cart = app('market_settings')->getCurrentShoppingCart();
                // $cart->removeProduct($product->getKey());

                $browser->screenshot('testProductToCart_1');

                $browser->visitRoute('product', ['product' => $product->web_uri])->assertSee(__("Detailed Item Information"));
                $browser->pause(2000);
                $browser->screenshot('testProductToCart_2');

                // click 4 times add/remove to cart
                $this->clickCartButton($browser);
                $browser->screenshot('testProductToCart_3');

                $this->clickCartButton($browser);
                $browser->screenshot('testProductToCart_4');

                $this->clickCartButton($browser);
                $browser->screenshot('testProductToCart_5');

                $this->clickCartButton($browser);
                $browser->screenshot('testProductToCart_6');

            } else {
                $this->fail('Product not found.');
            }

        });
    }

    /**
     * @param  Browser  $browser
     *
     * @return void
     * @throws \Facebook\WebDriver\Exception\TimeoutException
     */
    private function clickCartButton(Browser $browser): void
    {
        //
        $isInCartAtStart = false;
        $selectorCartButtonAdd = '.product-box .btn-add-to-cart';
        $selectorCartButtonRemove = '.product-box .btn-remove-from-cart';
        if ($e = $browser->element($selectorCartButtonRemove)) {
            $isInCartAtStart = true;
            // Log::debug("cart button: ", [$e->getText()]);
        } else {
            if ($e = $browser->element($selectorCartButtonAdd)) {
                // Log::debug("cart button: ", [$e->getText()]);
            }
        }

        // mini cart
        $selectorCartBadge = '.main-nav-bar .mini-cart .badge';
        // if ($e = $browser->element($selectorCartBadge.'[data-cart-loaded="1"]')) {
        // $browser->screenshot('testProductToCart_2');
        $cartBadgeElement = $browser->element($selectorCartBadge);
        $count = $cartBadgeElement->getText();

        //
        $isInCart = $isInCartAtStart;
        if ($e) {

            // Add or remove from cart ...
            $e->click();
            $isInCart = !$isInCart;
            if ($isInCart) {
                $browser->waitFor($selectorCartButtonRemove, seconds: self::maxWaitInSeconds);
                $count++;
            } else {
                $browser->waitFor($selectorCartButtonAdd, seconds: self::maxWaitInSeconds);
                $count--;
            }

            // Check mini cart is updating
            $browser->waitForTextIn($selectorCartBadge, $count, self::maxWaitInSeconds);
        }

    }

}
