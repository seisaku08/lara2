<!DOCTYPE html>
{{--

サイト全体をメンテナンスモード（システムは稼働しているが非公開）に移行するためのコマンド
php artisan down --render="errors::maintenance" --secret="systest"

php artisan down : laravelのメンテナンス移行コマンド
--render="errors::***" : メンテナンスモード時に表示するページの指定
    /resource/errors/フォルダの{***.blade.php}を参照する 
--secret="(パスフレーズ)" : メンテナンスモード時にもコンテンツにアクセスするためのパスフレーズ
    トップページURL/(パスフレーズ)　でアクセス可能

解除するときは
php artisan up

 --}}
<html>
    <head>
        <meta charset="utf-8">
        <title>メンテナンス中</title>
    </head>
    <body>
        <h1>只今メンテナンス中です。</h1>
    </body>
</html>