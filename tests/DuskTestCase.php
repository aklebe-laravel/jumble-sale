<?php

namespace Tests;

use Illuminate\Support\Collection;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;

abstract class DuskTestCase extends BaseTestCase
{
    const int maxWaitInSeconds = 15;

    const array users = [
        'admin2' => [
            'email' => 'AdminTest2@local.test',
            'password' => '1234567',
        ],
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
     * @return void
     * @throws \Throwable
     */
    protected function loginAdmin(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')->assertSee(__('Login'));
            $browser->screenshot('login before');
            $browser->type('email', data_get(static::users, 'admin2.email'))
                ->type('password', data_get(static::users, 'admin2.password'))
                ->press(__('Login'))
                ->waitForRoute('home', seconds: self::maxWaitInSeconds)
                // ->assertRouteIs('home');
                ->assertSee(__('Cart'));
            $browser->screenshot('login after');
        });
    }
}
