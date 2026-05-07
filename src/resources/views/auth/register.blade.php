<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}" />
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <img class="header__logo" src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH">
        </div>
    </header>
    <main>
        <div class="register">
            <div class="register__heading">
                <h1>会員登録</h1>
            </div>
            <form class="form" action="/register" method="post">
            @csrf
                <div class="form__group">
                    <div class="form__group-title">
                        <label for="name">名前</label>
                    </div>
                    <div class="form__group-content">
                        <div class="form__input--text">
                            <input type="text" id="name" name="name" value="{{ old('name') }}">
                        </div>
                        <div class="form__error">
                            @error('name')
                            {{ $message }}
                            @enderror
                        </div>
                    </div>
                </div>
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
                <div class="form__group">
                    <div class="form__group-title">
                        <label for="password_confirmation">パスワード確認</label>
                    </div>
                    <div class="form__group-content">
                        <div class="form__input--text">
                            <input type="password" id="password_confirmation" name="password_confirmation">
                        </div>
                        <div class="form__error">
                            @error('password')
                            {{ $message }}
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__button">
                    <button class="form__button-register" type="submit">登録する</button>
                </div>
                <a href="/login" class="form__link">ログインはこちら</a>
            </form>
        </div>
    </main>
</body>
</html>