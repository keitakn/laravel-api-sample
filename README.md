# laravel-api-sample

# クイックスタート

## 環境構築

[laravel-vagrant](https://github.com/keita-nishimoto/laravel-vagrant) を参考に環境構築を行って下さい。

## composer packageのインストール

ローカル開発環境の場合、以下のコマンドでpackageのインストールを行って下さい。

```
$ cd /home/vagrant/laravel-api-sample
$ composer install
```

## IDEでの開発効率を向上させる

_ide_helper.php を生成する事でIDEでのコード補完を可能にします。
以下のコマンドを実行しましょう。

```
$ cd /home/vagrant/laravel-api-sample
$ php artisan ide-helper:generate
```

（参考）[laravel-ide-helper](https://github.com/barryvdh/laravel-ide-helper)


PhpStorm、もしくはIntelliJ IDEA Ultimateを使っている人はIDEの専用プラグインを利用する事でさらに効率的になります。
※_ide_helper.phpだけでも十分なコード補完が出来るので興味ある人だけ見てください。

（参考）[Laravel IDE補完](http://blog.comnect.jp.net/blog/119)


# 参考リンク

- [公式ドキュメント](https://laravel.com/docs/5.3)
- [日本語ドキュメント](https://readouble.com/laravel/5.3/ja/)

コーディング規約（PSR-2）に違反しているコードをある程度自動整形してくれます。
ただし変数名の変更等の動作に影響がある場合は自動整形されません。
下記の規約にも目を通しておく事を推奨します。

- [PSR-1](http://www.php-fig.org/psr/psr-1/)
- [PSR-2](http://www.php-fig.org/psr/psr-2/)
- [PSR-4](http://www.php-fig.org/psr/psr-4/)
- [PSR-1（日本語訳）](http://www.infiniteloop.co.jp/docs/psr/psr-1-basic-coding-standard.html)
- [PSR-2（日本語訳）](http://www.infiniteloop.co.jp/docs/psr/psr-2-coding-style-guide.html)
- [PSR-4（日本語訳）](http://qiita.com/inouet/items/0208237629496070bbd4)

coding-check.sh はコードを自動整形しますが下記のコマンドで静的解析だけを実施する事が出来ます。

```
$ vendor/bin/phpcs --standard=PSR2 domain/
```

この例だとdomain配下のファイルを全て静的解析（整形はしない）します。
動作に影響があるような内容でも警告として出力してくれるので、IDE等で自動構文チェックを設定しておく事を推奨します。

# 参考リンク

- [公式ドキュメント](https://laravel.com/docs/5.3)
- [日本語ドキュメント](https://readouble.com/laravel/5.3/ja/)
