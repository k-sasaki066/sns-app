# SNS風アプリ
ログイン後、メッセージ投稿&削除、いいね登録&解除、コメント送信ができます。
<br>
コメント送信はリアルタイム通信ができます。
<br>

### TOPページ
<img width="1448" alt="トップ画面" src="https://github.com/user-attachments/assets/938ded7f-d277-4ceb-8256-5e5d776b726b" />
<br>

## 作成した目的
フロントエンド学習のアウトプットのため
<br>

## 機能一覧
|会員登録画面|ログイン画面|
| --- | --- |
|<img width="1400" alt="会員登録" src="https://github.com/user-attachments/assets/85e152a0-ba7c-4481-80ca-1094373b4ba5" />|<img width="1400" alt="ログイン" src="https://github.com/user-attachments/assets/13c1b2cc-8236-4a78-95f6-515e570d3020" />|
|Firebase Authenticationを使用し、ユーザー名、メールアドレス、パスワード、を入力して登録できます。|Firebase Authenticationを使用し、メールアドレス、パスワードを入力するとログインできます。|

|トップ画面|
| --- |
|<img width="1400" alt="トップ画面" src="https://github.com/user-attachments/assets/50317e76-d496-468e-a477-e4f161c41bbe" />|
|ログインユーザーのみアクセスできます。<br>メッセージ投稿とログアウト、投稿メッセージ一覧は下までスクロールすると追加で読み込まれます。<br>いいね登録&解除、自分が投稿したメッセージには削除ボタンが表示されます。|

|コメント詳細画面|エラー画面|
| --- | --- |
|<img width="1400" alt="コメント詳細画面" src="https://github.com/user-attachments/assets/cc376d7a-39ad-45e9-8138-5c7d015efffb" />|<img width="1400" alt="エラー画面" src="https://github.com/user-attachments/assets/5b1fd400-e1de-433d-8970-5d573314ecbc" />|
|トップ画面の詳細ボタンをクリックすると、対象メッセージとこれに対するコメントが表示されます。<br>コメントはPusherとEchoを使用してリアルタイム通信しています。|エラーが発生するとエラー画面に遷移します。ステータスとエラーの概要が表示されます。ホームボタンでトップ画面に戻れます。<br>画像は404エラー時の画面|

## 実行環境
Docker 27.5.1
<br>
nginx 1.21.1
<br>
php 8.3.8
<br>
mysql 8.0.26
<br>
phpMyAdmin 5.2.1
<br>
nuxt 3.17.1
<br>

## 使用技術
**フロントエンド**
<br>
Nuxt 3.17.1
<br>
Vue 3.5.13
<br>
Vue Router 4.5.1
<br>
Pinia 3.0.2
<br>
Axios 1.9.0
<br>
Firebase Authentication 11.6.1
<br>
vee-validate 4.15.0
<br>
Yup 1.6.1
<br>
Pusher JS 8.4.0
<br>
laravel-echo 2.1.3
<br>
<br>
**バックエンド**
<br>
Laravel Framework 8.83.8
<br>
PHP (^7.3 | ^8.0)
<br>
Kreait Firebase PHP SDK 6.4
<br>
Fruitcake Laravel CORS 2.0
<br>
Pusher PHP Server 7.2

## テーブル設計
<img width="748" alt="テーブル定義" src="https://github.com/user-attachments/assets/22dd170f-9285-4bac-80b0-39e6df99ec75" />

## ER図
<img width="661" alt="ER図" src="https://github.com/user-attachments/assets/7aec1c7b-8097-483f-843f-b4ed44b84fac" />


## 環境構築
<br>
① gitクローン

```
git clone https://github.com/k-sasaki066/sns-app.git
```
<br>
② docker composeのバージョン確認（バージョンによって一部記載が異なるため）

```
docker compose version
```
<br>
▫️ -v1の場合
<br>
<br>
docker-compose.ymlファイルのコメントアウトを外してください

```
version: '3.8'　(コメントアウト解除)
```
<br>
以下のdocker composeコマンドをdocker-composeに読み替えて実行してください
<br>
<br>
▫️ -v2の場合
<br>
変更点なし
<br>
<br>
③ dockerビルド

```
docker compose up -d --build
```
<br>

> _Mac の M1・M2 チップの PC で設定しています。エラーが発生する場合は、platform: linux/x86_64をコメントアウトしてください。_
> docker-compose.yml ファイルの「mysql」、「phpMyAdmin」の2箇所に記載があります。_

```bash
mysql:
    platform: linux/x86_64(この文をコメントアウト)
    image: mysql:8.0.26
    environment:
```

<br>
<br>
④ nuxt側、laravel側両方にenvファイルを作成

▫️ nuxt
<br>
nuxtディレクトリに移動

```
cd sns-app-nuxt
```
<br>

envファイルを作成

```
touch .env
```
<br>
<br>
▫️ laravel側
<br>
PHPコンテナに入る

```
docker compose exec php bash
```
<br>
envファイル作成

```
cp .example.env .env
```

<br>
<br>
⑤ composerインストール

```
composer install
```
<br>
<br>
⑥ envファイル編集
sns-app-laravel/src/.envファイル
<br>
▫️ アプリケーションキーを取得を取得

```
php artisan key:generate
```
<br>
▫️ mysqlの設定(docker-compose.ymlを参照)
  
  ```
  DB_CONNECTION=mysql
  DB_HOST=mysql(変更)
  DB_PORT=3306
  DB_DATABASE=laravel_db(変更)
  DB_USERNAME=laravel_user(変更)
  DB_PASSWORD=laravel_pass(変更)
  ```
  <br>
▫️ Pusherの設定（公式ページhttps://pusher.com/laravel/）
<br>
1. Pusherのアカウントを作成する
<img width="700" alt="2 アカウント作成" src="https://github.com/user-attachments/assets/1c6705f2-c846-483f-980e-d1402812976a" />
<br>
<br>
2. アカウント作成後の画面。チャンネルを作成するためChannelsの『Get started』をクリック
<br>
<img width="700" alt="3 Channels選択" src="https://github.com/user-attachments/assets/9f8ca085-2404-49c1-840a-12eed3146a81" />
<br>
<br>
3. app名（任意の名前）、クラスターのデータセンター（ap3を選択）、フロントエンドにVue.js、バックエンドにLaravelを選択、Create appボタンをクリック
<br>
<img width="700" alt="4 アプリ作成" src="https://github.com/user-attachments/assets/1a47af3c-2c47-4fcc-ac35-b3bd6a03831a" />
<br>
<br>
4. 左メニューのAPP Keysをクリックするとappキーが表示されます
<br>
<img width="700" alt="appキー取得" src="https://github.com/user-attachments/assets/5732167f-72ef-4716-bc50-4c6bb503d45d" />
<br>
<br>
5.  /sns-app-laravel/src/.envファイルに追加

  ```
  BROADCAST_DRIVER=pusher
  
  PUSHER_APP_ID=your_app_id
  PUSHER_APP_KEY=your_key
  PUSHER_APP_SECRET=your_secret
  PUSHER_APP_CLUSTER=your_cluster
  ```
<br>
6. /sns-app-nuxt/.envファイルに追加

  ```
  NUXT_PUBLIC_PUSHER_APP_KEY=your_key
  NUXT_PUBLIC_PUSHER_APP_CLUSTER=your_cluster
  ```
<br>
<br>
▫️ Firebaseプロジェクト作成（公式ページ https://pusher.com/laravel/](https://firebase.google.com/?hl=ja）
<br>
1. Firebaseアカウントを作成する（アカウント作成するには、Googleアカウントが必要です。）
<br>
<img width="700" alt="トップ画面" src="https://github.com/user-attachments/assets/95c8afe0-b847-4edb-85c1-77eb983efd32" />
<br>
<br>
2. ログイン後、コンソール画面にて『Firebaseプロジェクトを使ってみる』をクリック
<br>
<img width="700" alt="1 プロジェクト作成" src="https://github.com/user-attachments/assets/86ab9923-29df-47d4-a348-a3155d626c78" />
<br>
<br>
3. プロジェクト名を入力する（任意の名前）
<br>
<img width="700" alt="2 プロジェクト名" src="https://github.com/user-attachments/assets/cd77cf4c-a006-49bf-aefc-c886349c1c08" />
<br>
<br>
4. Gemini（生成AI）の設定をして続行ボタンをクリック
<br>
<img width="700" alt="3 gemini設定" src="https://github.com/user-attachments/assets/58c763f6-0d6f-4a0b-80e8-b08d0e699d43" />
<br>
<br>
5. Google アナリティクスを有効にするか選択→ オフでも問題なし（後から有効化可能）
<br>
<img width="700" alt="4 アナリティクス" src="https://github.com/user-attachments/assets/ac00cfd3-7c40-4823-ac4a-34566f7c4804" />
<br>
<br>
6. プロジェクト作成ボタンをクリック
<br>
<img width="700" alt="5 作成" src="https://github.com/user-attachments/assets/8d396c8a-27cd-4b10-a118-c4aa27d51b35" />
<br>
<br>
▫️ Firebase Authentication設定手順
<br>
1. コンソール画面左のメニューから『構築』→『Authentication』をクリック
<br>
<img width="700" alt="1 authentication設定" src="https://github.com/user-attachments/assets/ed2c678f-450e-418d-929d-f71d06768432" />
<br>
<br>
2. 始める』ボタンをクリック→表示される項目から『メール / パスワード』をクリック
<br>
<img width="700" alt="2 Authenticationクリック後" src="https://github.com/user-attachments/assets/cefef638-e3ea-4a5d-862a-a3a1eee2b795" />
<br>
<br>
3. メール / パスワードを有効にする
<br>
<img width="700" alt="3 有効にする" src="https://github.com/user-attachments/assets/ef834190-a878-481d-ae75-3d5be12e0d63" />
<br>
<br>
▫️ Firebase アプリ登録設定手順
<br>
1. コンソール画面の左メニューから『Authentication』→『プロジェクトの設定』をクリック
<br>
<img width="700" alt="4 プロジェクト設定ボタン" src="https://github.com/user-attachments/assets/6352720f-3991-48e2-b28d-06d38c21b999" />
<br>
<br>
2. 画像赤枠のwebボタンをクリック
<br>
<img width="700" alt="5 webボタン" src="https://github.com/user-attachments/assets/772aa1a2-175d-44af-9179-c47d8a2d8bd4" />
<br>
<br>
3. アプリのニックネームを設定（任意の名前）して、『アプリを登録』をクリック（「Firebase Hosting を設定する」は今回は必要無いのでOFFでOK）
<br>
<img width="700" alt="6 ネーム設定" src="https://github.com/user-attachments/assets/37388415-343c-479f-a798-11d66eceb00c" />
<br>
<br>
4. 『<script>タグを使用する』を選択し、『コンソールへ進む』をクリック→Firebase 構成情報が表示されます
<br>
<img width="700" alt="7 SDK取得" src="https://github.com/user-attachments/assets/b556c310-5c2e-41c5-b928-b388aeef2987" />
<br>
<br>
▫️ Firebase 秘密鍵設定手順（Firebase Admin SDK用の秘密鍵を発行する操作）
<br>
1. コンソール画面から⚙️マーク→『プロジェクトの設定』→『サービスアカウント』をクリック
　　<br>
   Node.jsを選択し、『新しい秘密鍵を生成』をクリック
   <br>
   <img width="700" alt="8 秘密鍵生成" src="https://github.com/user-attachments/assets/e7513d29-4d47-4282-8c86-72b3cdf317ca" />
<br>
<br>
2. クリック後、.jsonファイルが自動でダウンロードされます<br>ファイル名を『firebase-adminsdk.json』に変更する<br>このファイルを/sns-app-laravel/src/firebaseディレクトリに保存
<br>
<br>
【重要】秘密鍵ファイルは機密情報のためGithubにアップロードできません。<br>/sns-app-laravel/src/gitignoreファイルに以下の記述があることを確認

  ```
  /firebase/*.json
  ```

<br>
<br>
3. /sns-app-laravel/src/.envファイルに追加

```
GOOGLE_APPLICATION_CREDENTIALS=firebase-adminsdk.jsonの絶対パス
FIREBASE_PROJECT_ID=プロジェクトID(コンソール画面から取得)
```
<br>
<br>
4. /sns-app-nuxt/.envファイルに追加(コンソール画面のSDK(firebaseConfig)から取得)

```
NUXT_PUBLIC_FIREBASE_API_KEY=apiKey
NUXT_PUBLIC_FIREBASE_AUTH_DOMAIN=authDomain
NUXT_PUBLIC_FIREBASE_PROJECT_ID=projectId
NUXT_PUBLIC_FIREBASE_STORAGE_BUCKET=storageBucket
NUXT_PUBLIC_FIREBASE_MESSAGING_SENDER_ID=messagingSenderId
NUXT_PUBLIC_FIREBASE_APP_ID=appId
```

<br>
<br>
⑦ テーブル作成
  
  ```
  php artisan migrate
  ```
<br>
<br>
⑧ http://localhost:3000/registerを開き、任意のユーザーを3人作成する（実際に登録されたユーザーをダミーデータに使用するため）
<br>
作成例
<br>
<img width="700" alt="firebaseユーザー作成例" src="https://github.com/user-attachments/assets/0a0b9be7-8a7a-4d6e-9077-c25b94980cbf" />
<br>
<br>
⑨ /sns-app-laravel/src/.envファイルに追加

```
FIREBASE_UID_1=1人目のfirebaseユーザーuid
FIREBASE_UID_2=2人目のfirebaseユーザーuid
FIREBASE_UID_3=3人目のfirebaseユーザーuid
```
<br>
<br>
⑩ .envファイルの変更を反映

```
php artisan cache:clear
php artisan config:clear
```
<br>
<br>
11. ダミーデータ作成

```
php artisan migrate:fresh --seed
 ```
<br>
<br>
## URL

- 開発環境
  - ログインページ <http://localhost:3000/login>
- MailHog <http://localhost:8025>
- phpMyAdmin <http://localhost:8080>










