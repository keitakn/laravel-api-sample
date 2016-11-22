<?php
/**
 * テスト基底クラス
 *
 * @author keita-nishimoto
 * @since 2016-09-08
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Tests;

use App\Console\Kernel;

/**
 * Class AbstractTestCase
 *
 * @category laravel-api-sample
 * @package Tests
 * @author keita-nishimoto
 * @since 2016-09-08
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
abstract class AbstractTestCase extends \Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'https://dev.laravel-api.net';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->beforeApplicationDestroyed(function () {
            \DB::disconnect();
        });

        parent::tearDown();
    }
}
