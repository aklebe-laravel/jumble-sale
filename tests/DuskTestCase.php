<?php

namespace Tests;

use Illuminate\Support\Collection;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;
use Throwable;

abstract class DuskTestCase extends BaseTestCase
{
    const int maxWaitInSeconds = 15;

    /**
     * @var array|string[]
     */
    protected array $selectors = [
        'mini-cart'          => '.main-nav-bar .mini-cart',
        'mini-cart-badge'    => '.main-nav-bar .mini-cart .badge',
        'offer-form'         => '.dt-form-type-Offer',
        'offer-item-form'    => '.dt-form-type-OfferItem',
        'opened-form'        => '.dt-edit-form',
        'offer-dt'           => '.dt-auto-Offer-1',
        'offer-dt-first-row' => '.dt-auto-Offer-1 tr:first-child',
        'product-form'       => '.dt-form-type-Product',
        'message-box'        => '.modal-message-box',
    ];

    /**
     * Prepare for Dusk test execution.
     */
    #[BeforeClass]
    public static function prepare(): void
    {
        if (!static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
            '--ignore-certificate-errors',
            '--ignore-ssl-errors',
            '--disable-smooth-scrolling', // can fix some not clickable errors
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                '--headless=new',
            ]);
        })->all());

        //return RemoteWebDriver::create($_ENV['DUSK_DRIVER_URL'] ?? 'http://localhost:9515', DesiredCapabilities::chrome());
        return RemoteWebDriver::create($_ENV['DUSK_DRIVER_URL'] ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(ChromeOptions::CAPABILITY,
                $options));
    }

    /**
     * Determine whether the Dusk command has disabled headless mode.
     */
    protected function hasHeadlessDisabled(): bool
    {
        return isset($_SERVER['DUSK_HEADLESS_DISABLED']) || isset($_ENV['DUSK_HEADLESS_DISABLED']);
    }

    /**
     * Determine if the browser window should start maximized.
     */
    protected function shouldStartMaximized(): bool
    {
        return isset($_SERVER['DUSK_START_MAXIMIZED']) || isset($_ENV['DUSK_START_MAXIMIZED']);
    }

    /**
     * @param  string  $configKey
     *
     * @return void
     * @throws Throwable
     */
    protected function loginDuskUser(string $configKey): void
    {
        $this->browse(function (Browser $browser) use ($configKey) {
            $browser->visit('/')->assertSee(__('Login'));
            $browser->screenshot('login before');
            $browser->type('email', config('dusk.users.'.$configKey.'.name'))->type('password', config('dusk.users.'.$configKey.'.password'))->press(__('Login'))->waitForRoute('home', seconds: self::maxWaitInSeconds)
                // ->assertRouteIs('home');
                ->assertSee(__('Cart'));
            $browser->screenshot('login after');
        });
    }

    /**
     * @param  Browser  $browser
     *
     * @return int
     */
    protected function getCartItemCount(Browser $browser): int
    {

        return (int) trim($browser->text($this->selector('mini-cart-badge')));
    }

    /**
     * @param  Browser  $browser
     * @param  string   $text
     *
     * @return bool
     * @throws \Facebook\WebDriver\Exception\TimeoutException
     */
    protected function waitForCartItemCount(Browser $browser, string $text): bool
    {
        $browser->waitForTextIn($this->selector('mini-cart-badge'), $text, self::maxWaitInSeconds);

        return true;
    }

    /**
     * @param  Browser  $browser
     * @param  string   $methodName
     * @param  string   $suffix
     *
     * @return void
     */
    protected function methodScreenshot(Browser $browser, string $methodName, string $suffix): void
    {
        $str = str_replace('::', '_', app('system_base')->getSimpleClassName($methodName));
        $str .= '_'.$suffix;
        $browser->screenshot($str);
    }

    /**
     * @param ...$selectorConfigs
     *
     * @return string
     */
    protected function selector(...$selectorConfigs): string
    {
        $selector = '';
        foreach ($selectorConfigs as $selectorKey) {
            if ($selector) {
                $selector .= ' ';
            }
            if (isset($this->selectors[$selectorKey])) {
                $selector .= $this->selectors[$selectorKey];
            } else {
                $selector .= $selectorKey;
            }
        }

        //Log::debug($selector, [__METHOD__]);

        return $selector;
    }

    /**
     * @param  Browser      $browser
     * @param  string       $formConfigSelector
     * @param  string       $formActionConfigSelector
     * @param  string|null  $messageBoxActionConfigSelector
     * @param  string|null  $waitForConfigSelector
     * @param  bool         $waitForCloseForm
     *
     * @return void
     * @throws \Facebook\WebDriver\Exception\ElementClickInterceptedException
     * @throws \Facebook\WebDriver\Exception\NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\TimeoutException
     */
    protected function clickFormActionAndWaitFor(
        Browser $browser,
        string $formConfigSelector,
        string $formActionConfigSelector,
        ?string $messageBoxActionConfigSelector = null,
        ?string $waitForConfigSelector = null,
        bool $waitForCloseForm = true
    ): void {
        // Scroll to the form action and click it
        $formActionConfigSelector = $this->selector($formConfigSelector, '.form-actions', $formActionConfigSelector);
        $browser->waitFor($formActionConfigSelector, self::maxWaitInSeconds);
        $browser->scrollTo($formActionConfigSelector);
        $browser->click($formActionConfigSelector);

        // Click action in confirmation message box
        if ($messageBoxActionConfigSelector !== null) {
            $messageBoxActionConfigSelector = $this->selector('message-box', $messageBoxActionConfigSelector);
            $browser->assertVisible($messageBoxActionConfigSelector)->click($messageBoxActionConfigSelector);
        }

        if ($waitForCloseForm) {
            $browser->waitUntilMissing($this->selector($formConfigSelector, 'opened-form'), self::maxWaitInSeconds);
        }

        $browser->waitUntilMissing($this->selector($formConfigSelector, '.fullscreen-overlay'), self::maxWaitInSeconds);

        // wait if needed
        if ($waitForConfigSelector !== null) {
            $waitForConfigSelector = $this->selector($waitForConfigSelector);
            $browser->waitFor($waitForConfigSelector, self::maxWaitInSeconds);
        }
    }

    //protected function formTask(Browser $browser, array $config)
    //{
    //    $config = [
    //        'form'                   => 'offer-form',
    //        'form_action'            => '.btn-offer-reject',
    //        'form_callback_task'     => null,
    //        'message_box_action'     => '.btn-offer-reject',
    //        'finish_wait_selector'   => '.dt-column-status .offer-status-REJECTED',
    //        'form_callback_finished' => null,
    //    ];
    //}

}
