<?php

namespace Tests\Browser;

use Exception;
use Laravel\Dusk\Browser;
use Modules\Market\app\Models\User;
use Modules\Market\app\Services\OfferService;
use Modules\Market\app\Services\ShoppingCartService;
use Tests\DuskTestCase;

class OfferProcessTest extends DuskTestCase
{
    /**
     * Login user
     */
    public function testLogin(): void
    {
        //$this->loginDuskUser('trader1');
    }

    /**
     * Full test a complete offer
     *
     * - check trader 2 exists
     * - login trader 1
     * - clearing cart
     * - navigate to trader2 user profile
     * - find a product in trader2 product list and navigate to this product
     * - add this product to cart
     * - add 2 more products from user products carousel
     * - check 3 products in cart now
     * - ...
     * - clearing cart
     *
     */
    public function testShoppingCart(): void
    {
        $methodName = __METHOD__;
        $this->browse(function (Browser $browser) use ($methodName) {

            //  maximize() can fix "element click intercepted: Element is not clickable at point ..."
            $browser->maximize();
            //$browser->fitContent();

            // find user trader2 ...
            /** @var User $trader2 */
            if (!($trader2 = app(User::class)->where('name', config('dusk.users.trader2.name'))->first())) {
                throw new Exception('Trader 2 not found');
            }

            // find user trader1 ...
            /** @var User $trader1 */
            if (!($trader1 = app(User::class)->where('name', config('dusk.users.trader1.name'))->first())) {
                throw new Exception('Trader 1 not found');
            }
            // clear shopping cart
            $cart = app(ShoppingCartService::class)->getCurrentShoppingCart($trader1->getKey());
            $cart->removeItems();
            // remove all orders created by user
            $orders = app(OfferService::class)->getOffersCreatedByUser($trader1->getKey());
            $orders->delete();

            // ---------------------------------------------------------
            // login trader1
            // ---------------------------------------------------------
            $browser->loginAs($trader1->getKey());

            // navigate to user trader2 profile ...
            $browser->visit($trader2->getUserProfileLink())->assertVisible('.website-base-dt-forms .dt-edit-form');

            // go to tab products and click the first product in datatable ...
            $browser->with('.website-base-dt-forms .dt-edit-form', function (Browser $form) {
                $tabPane = '.responsive-tabs:nth-child(1) .tab-pane:nth-child(2)';
                $form->click('.responsive-tabs:nth-child(1) ul li:nth-child(2)')->assertVisible($tabPane);
                $form->with($tabPane, function (Browser $tabContent) {
                    $tabContent->click('@link-name');
                });
            });

            // check whether we are in product detail view ...
            $browser->assertSee(__("Detailed Item Information"));
            $this->methodScreenshot($browser, $methodName, 'product_view_1');

            // add to cart ...
            $browser->with('.product-box .info-container', function (Browser $box) use ($methodName) {
                $selectorCartButtonAdd = '.btn-add-to-cart';
                $selectorCartButtonRemove = '.btn-remove-from-cart';
                $box->waitFor($selectorCartButtonAdd, seconds: self::maxWaitInSeconds)->click($selectorCartButtonAdd);
                $box->waitFor($selectorCartButtonRemove, seconds: self::maxWaitInSeconds);
                $this->methodScreenshot($box, $methodName, 'added_cart_1');

            });

            // go to related products and add another product to cart ...
            $browser->with('.userProductsCarousel', function (Browser $box) use ($methodName) {
                $carouselProductBtnAddToCart = '.multi-carousel-item .btn-add-to-cart';
                $carouselProductBtnRemoveFromCart = '.multi-carousel-item .btn-remove-from-cart';
                // wait for javascript is ready to render carousel
                $box->waitFor($carouselProductBtnAddToCart, self::maxWaitInSeconds);
                $elements = $box->elements($carouselProductBtnAddToCart);
                for ($i = 0; $i < 2; $i++) {
                    $box->scrollTo($carouselProductBtnAddToCart);
                    if (!count($elements)) {
                        break;
                    }
                    // add to cart
                    $elements[0]->click();
                    //$elements[0]->waitFor('.btn-remove-from-cart');
                    array_shift($elements);
                    $this->methodScreenshot($box, $methodName, 'carousel_product_'.($i + 1));
                }
            });

            // we should have 3 items in cart now
            $this->waitForCartItemCount($browser, 3);

            // Click Mini Cart to show cart
            $browser->scrollTo($this->selector('mini-cart'))->click($this->selector('mini-cart'));

            // Click list potential orders
            $btnOfferPrepare = '.btn-potential-offers';
            $browser->assertVisible($btnOfferPrepare)->click($btnOfferPrepare);

            // Click create order and edit (to make order with status APPLIED)
            $btnOfferCreate = '.btn-create-and-edit-offer';
            $browser->assertVisible($btnOfferCreate)->click($btnOfferCreate);

            // form: create order
            $this->clickFormActionAndWaitFor($browser, 'offer-form', '.btn-offer-create-binding', '.btn-offer-create-binding', $this->selector('offer-dt-first-row', '.dt-column-status .offer-status-NEGOTIATION'));

            // ---------------------------------------------------------
            // login trader2
            // ---------------------------------------------------------
            $browser->logout();
            // login
            $browser->loginAs($trader2->getKey());

            // visit offer page
            $browser->visit(route('manage-data', ['modelName' => 'Offer']));

            // Click open form first offer
            $btnEditOffer = $this->selector('offer-dt-first-row', '.btn-edit');
            $browser->assertVisible($btnEditOffer)->click($btnEditOffer);

            // form: reject order
            $this->clickFormActionAndWaitFor($browser, 'offer-form', '.btn-offer-reject', '.btn-offer-reject', $this->selector('offer-dt-first-row', '.dt-column-status .offer-status-REJECTED'));

            // ---------------------------------------------------------
            // login trader1
            // ---------------------------------------------------------
            $browser->logout();
            // login
            $browser->loginAs($trader1->getKey());

            // visit offer page
            $browser->visit(route('manage-data', ['modelName' => 'Offer']));

            // Click open form first offer
            $btnEditOffer = $this->selector('offer-dt-first-row', '.btn-edit');
            $browser->assertVisible($btnEditOffer)->click($btnEditOffer);

            // wait for form to create new offer
            $btnOfferReOffer = '.form-actions .btn-form-offer-re-offer';
            $browser->waitFor($btnOfferReOffer, self::maxWaitInSeconds)->click($btnOfferReOffer);

            // Click "ReOffer" in confirmation message box
            $btnMsgBoxReOffer = '.modal-message-box .btn-messagebox-offer-re-offer';
            $browser->waitFor($btnMsgBoxReOffer)->click($btnMsgBoxReOffer);

            // check datatable new order status APPLIED
            $statusApplied = $this->selector('offer-dt-first-row', '.dt-column-status .offer-status-APPLIED');
            $browser->waitFor($statusApplied, self::maxWaitInSeconds);

            // Click open form first offer
            $btnEditOffer = $this->selector('offer-dt-first-row', '.btn-edit');
            $browser->assertVisible($btnEditOffer)->click($btnEditOffer);

            // wait for form
            $btnOfferReject = '.form-actions .btn-offer-create-binding';
            $browser->waitFor($btnOfferReject, self::maxWaitInSeconds);

            // Click open form first offer item
            $btnEditOfferItem = '.dt-auto-OfferItem-1 tr:first-child .btn-edit';
            $browser->assertVisible($btnEditOfferItem)->click($btnEditOfferItem);

            // wait for OrderItem form
            $browser->waitFor($this->selector('offer-item-form', 'opened-form'));

            // change input price
            $this->methodScreenshot($browser, $methodName, 'price_before');
            $inputPrice = $this->selector('offer-item-form', 'input[name="price"]');
            $browser->scrollTo($inputPrice);
            $price = $browser->inputValue($inputPrice);
            $price -= 10;
            if ($price < 0) {
                $price = 222;
            }
            $browser->type($inputPrice, $price);
            $this->methodScreenshot($browser, $methodName, 'price_after');

            // sub form: finish order item
            $this->clickFormActionAndWaitFor($browser, 'offer-item-form', '.form-action-accept');

            // form: finish order
            $this->clickFormActionAndWaitFor($browser, 'offer-form', '.btn-offer-create-binding', '.btn-offer-create-binding', $this->selector('offer-dt-first-row', '.dt-column-status .offer-status-NEGOTIATION'));



            // end ...

            //// remove all items
            //$cart->refresh();
            //$cart->removeItems();
            //$orders->delete();

            $this->methodScreenshot($browser, $methodName, 'end');
        });
    }

}
