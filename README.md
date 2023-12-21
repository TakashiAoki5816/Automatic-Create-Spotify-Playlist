<p align="center">
    <img src="https://img.shields.io/badge/PHP-v8.1.26-informational?style=plastic&logo=php" alt="PHP Version">
    <img src="https://img.shields.io/badge/Laravel-v9.40.1-orange?style=plastic&logo=laravel" alt="Laravel Version">
    <img src="https://img.shields.io/badge/Vue.js-v3.3.4-success?style=plastic&logo=vue.js" alt="Vue.js Version">
    <img src="https://img.shields.io/badge/TypeScript-v3.3.4-informational?style=plastic&logo=typescript" alt="TypeScript Version">
    <img src="https://img.shields.io/badge/Tailwind CSS-v3.3.6-9cf?style=plastic&logo=tailwindcss" alt="Tailwind CSS Version">
</p>

# Automatic Create Spotify Playlist

単一または複数のプレイリストから選択したジャンルだけを抽出したプレイリストを作成する

## About

Spotify 認証を行い、Spotify API のアクセストークンを取得
認証を行うとリダイレクトされ、ホームページが表示される
対象とするプレイリスト, 作成するプレイリストの名前, 抽出するジャンルを選択し作成ボタンを押下
※100 曲程度ならこの手順で済むが 1000 曲単位になってくると現状エラーになってしまう → デバッガーでブレイクポイントを指定しながらだとできるが要修正

## Usage

1. `$ git clone https://github.com/TakashiAoki5816/Automatic-Create-Spotify-Playlist.git`
2. `$ cp .env.example .env | cp ./docker/.env.example ./docker/.env`
3. `$ composer require laravel/sail --dev`
4. `$ php artisan sail:install`
5. `$ composer install`
6. `$ ./vendor/bin/sail npm install`
7. Access to [Spotify for Developers](https://developer.spotify.com/dashboard) after that Create app
8. Set env from Settings
9. ./vendor/bin/sail php -v
10. ./vendor/bin/sail npm run dev

```
SPOTIFY_CLIENT_ID=
SPOTIFY_CLIENT_SECRET=
```
