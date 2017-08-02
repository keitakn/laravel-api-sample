<?php
/**
 * テスト基底クラス
 *
 * @author keita-nishimoto
 * @since 2016-09-08
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Class AbstractTestCase
 *
 * @category laravel-api-sample
 * @package Tests
 * @author keita-nishimoto
 * @since 2016-09-08
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
abstract class AbstractTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'https://dev.laravel-api.net';


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
