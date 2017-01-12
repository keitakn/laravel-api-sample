<?php
/**
 * PHP-CS-Fixerの設定ファイル
 *
 * @author keita-nishimoto
 * @since 2016-09-26
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 * @link https://github.com/FriendsOfPHP/PHP-CS-Fixer
 */

$finder = PhpCsFixer\Finder::create()
    ->exclude('bootstrap/cache')
    ->exclude('storage')
    ->exclude('vendor')
    ->in(__DIR__);

$config = PhpCsFixer\Config::create();
$rules  = [
    '@PSR1' => false,
    '@PSR2' => true,
    // 配列は[]に統一
    'array_syntax' => ['syntax' => 'short'],
    // =の位置を揃える
    'binary_operator_spaces' => false,
    // => の位置を揃える
    'binary_operator_spaces' => false,
    // 演算子 => は複数行の空白に囲まれない
    'no_multiline_whitespace_around_double_arrow' => true,
    // セミコロンを閉じる前の複数行の空白は禁止
    'no_multiline_whitespace_before_semicolons' => true,
    // 未使用のuse文は削除
    'no_unused_imports' => true,
    // use文の整列
    'ordered_imports' => true,
    // 単純な文字列の二重引用符を一重引用符に変換
    'single_quote' => true,
    //
];

$config->setRules($rules)
    ->setUsingCache(true)
    ->setFinder($finder);

return $config;
