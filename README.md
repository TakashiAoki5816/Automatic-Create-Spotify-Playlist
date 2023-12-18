<p align="center">
    <img src="https://img.shields.io/badge/Laravel-v9.40.1-orange?style=plastic&logo=laravel" alt="Laravel Version">
    <img src="https://img.shields.io/badge/PHP-v8.1.26-informational?style=plastic&logo=php" alt="PHP Version">
    <img src="https://img.shields.io/badge/Vue.js-v3.3.4-success?style=plastic&logo=vue.js" alt="Vue.js Version">
    <img src="https://img.shields.io/badge/TypeScript-v3.3.4-informational?style=plastic&logo=typescript" alt="TypeScript Version">
    <img src="https://img.shields.io/badge/Tailwind CSS-v3.3.6-9cf?style=plastic&logo=tailwindcss" alt="Tailwind CSS Version">
</p>

## About Automatic-Spotify-App

単一または複数のプレイリストから選択したジャンルだけを抽出したプレイリストを作成する

## Usage

Spotify 認証を行い、Spotify API のアクセストークンを取得
認証を行うとリダイレクトされ、トップページが表示される
対象とするプレイリスト、作成するプレイリストの名前、抽出するジャンルを選択し作成ボタンを押下
※100 曲程度ならこの手順で済むが 1000 曲単位になってくると現状エラーになってしまう → デバッガーでブレイクポイントを指定しながらだとできるが要修正
