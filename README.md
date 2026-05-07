# 勤怠管理アプリ

## 概要
本プロジェクトは、Laravelを使用したフリマアプリケーションです。出退勤、修正の申請、承認が行えます。

## Dockerビルド
- git clone git@github.com:asahi-1029/attendance-management.git
- docker-compose up -d --build

## Laravel環境構築
- docker-compose exec php bash
- composer install
- cp .env.example .env

### データベース設定
.envファイルを以下のように設定してください。
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

MAIL_FROM_ADDRESS=example@example.com
MAIL_FROM_NAME="Attendance Management"
```

- php artisan key:generate
- php artisan migrate
- php artisan db:seed

## 開発環境

### 認証必須
- ログアウト : http://localhost/logout
- 出勤登録画面（一般ユーザー）: http://localhost/attendance
- 勤怠一覧画面（一般ユーザー）: http://localhost/attendance/list
- 勤怠詳細画面（一般ユーザー）: http://localhost/attendance/detail/{id}
- 申請一覧画面（一般ユーザー）: http://localhost/stamp_correction_request/list
- 勤怠一覧画面（管理者） : http://localhost/admin/attendance/list
- 勤怠詳細画面（管理者） : http://localhost/admin/attendance/{id}
- スタッフ一覧画面（管理者） : http://localhost/admin/staff/list
- スタッフ別勤怠一覧画面（管理者）: http://localhost/admin/attendance/staff/{id}
- 申請一覧画面（管理者）: http://localhost/stamp_correction_request/list
- 申請一覧画面（管理者） : http://localhost/stamp_correction_request/approve/{attendance_correct_request_id}

### 認証関連
- 会員登録画面 （一般ユーザー） : http://localhost/register
- ログイン画面 （一般ユーザー） : http://localhost/login
- ログイン画面（管理者） : http://localhost/admin/login
- メール認証誘導画面 : http://localhost/email/verify

### 開発環境ツール
- phpMyadmin : http://localhost:8080/
- mailhog : http://localhost:8025/

## 使用技術（実行環境）
- PHP 8.1.x
- Laravel 8.83.8
- HTML/CSS
- JavaScript（Vanilla JS）
- MySQL 8.0.26
- nginx 1.21.1
- phpMyAdmin
- MailHog

## テスト手順

### テスト用データベース作成

MySQLコンテナへ接続

- docker-compose exec mysql bash
- mysql -u root -p

テスト用データベースを作成
- CREATE DATABASE demo_test;

### テスト用環境ファイル作成
- cp .env .env.testing

### .env.testing のデータベース設定
`.env.testing` を以下のように設定してください。
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=demo_test
DB_USERNAME=root
DB_PASSWORD=root
```

### アプリケーションキー生成
- php artisan key:generate --env=testing

### マイグレーション
- php artisan migrate --env=testing

### テスト実行
設定キャッシュをクリア
- php artisan config:clear
テストを実行
- vendor/bin/phpunit

## ER図
<img width="1040" height="808" alt="image" src="https://github.com/user-attachments/assets/a6e3418b-7fa3-4069-97cb-97d3daf7cda7" />
