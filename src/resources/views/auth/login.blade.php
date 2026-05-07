<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}" />
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <img class="header__logo" src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH">
        </div>
    </header>
    <main>
        <div class="login">
            <div class="login__heading">
                <h1>ログイン</h1>
            </div>
            <form class="form" action="/login" method="post">
            @csrf
                <div class="form__group">
                    <div class="form__group-title">
                        <label for="email">メールアドレス</label>
                    </div>
                    <div class="form__group-content">
                        <div class="form__input--text">
                            <input type="text" id="email" name="email" value="{{ old('email') }}">
                        </div>
                        <div class="form__error">
                            @error('email')
                            {{ $message }}
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group">
                    <div class="form__group-title">
                        <label for="password">パスワード</label>
                    </div>
                    <div class="form__group-content">
                        <div class="form__input--text">
                            <input type="password" id="password" name="password">
                        </div>
                        <div class="form__error">
                            @error('password')
                            {{ $message }}
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__button">
                    <button class="form__button-login" type="submit">ログインする</button>
                </div>
                <a href="/register" class="form__link">会員登録はこちら</a>
            </form>
        </div>
    </main>
</body>
</html>