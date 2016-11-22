<?php
/**
 * ValidatorServiceProvider
 *
 * @author keita-nishimoto
 * @since 2016-10-31
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */

namespace App\Providers;

use Domain\CustomValidator;
use Illuminate\Support\ServiceProvider;

/**
 * Class ValidatorServiceProvider
 *
 * @category laravel-api-sample
 * @package App\Providers
 * @author keita-nishimoto
 * @since 2016-10-31
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        \Validator::resolver(function ($translator, $data, $rules, $messages) {
            return new CustomValidator($translator, $data, $rules, $messages);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
