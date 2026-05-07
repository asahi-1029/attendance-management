<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/auth/verify-email.css') }}" />
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <img class="header__logo" src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH">
        </div>
    </header>
    <main>
        <p class="main__content">
            登録して頂いたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </p>

        <div>
            <button class="form__button-auth" type="button" onclick="location.href='http://localhost:8025'">
                認証はこちらから
            </button>
        </div>

        <div>
            <form method="POST" action="/email/verification-notification">
                @csrf
                <button type="submit" class="form__link">
                    認証メールを再送する
                </button>
            </form>
        </div>
    </main>
</body>
</html>