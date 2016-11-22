<?php
/**
 * PHP-CS-Fixerの設定ファイル
 *
 * @author keita-nishimoto
 * @since 2016-09-26
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 * @link https://github.com/FriendsOfPHP/PHP-CS-Fixer
 */
$finder = Symfony\CS\Finder::create()
    ->exclude('bootstrap/cache')
    ->exclude('storage')
    ->exclude('vendor')
    ->in(__DIR__);

return Symfony\CS\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers(['-psr0'])
    ->finder($finder);
