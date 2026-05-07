<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/stamp_correction_request/index.css') }}" />
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <img class="header__logo" src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH">
            <nav>
                <ul class="header__nav">
                    <li><a href="/attendance">勤怠</a></li>
                    <li><a href="/attendance/list">勤怠一覧</a></li>
                    <li><a href="/stamp_correction_request/list">申請</a></li>
                    <li>
                        <form action="/logout" method="post">
                            @csrf
                            <button type="submit" class="header__link-logout">ログアウト</button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <div class="application">
            <h1 class="application__title">
                <span class="title__bar"></span>
                申請一覧
            </h1>
            <div class="tab">
                <a href="/stamp_correction_request/list?page=pending" class="{{ $page === 'pending' ? 'active' : '' }}">承認待ち</a>
                <a href="/stamp_correction_request/list?page=approved" class="{{ $page === 'approved' ? 'active' : ''}}">承認済み</a>
            </div>
            <hr class="divider">
            <table class="application-table">
                <thead>
                    <tr>
                        <th>状態</th>
                        <th>名前</th>
                        <th>対象日時</th>
                        <th>申請理由</th>
                        <th>申請日時</th>
                        <th>詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $request)
                        <tr>
                            <td>{{ $request->status_label }}</td>
                            <td>{{ $request->user->name }}</td>
                            <td>{{ $request->attendance->date->format('Y/m/d') }}</td>
                            <td>{{ $request->note }}</td>
                            <td>{{ $request->created_at->format('Y/m/d') }}</td>
                            <td><a href="/attendance/detail/{{ $request->attendance->id }}">詳細</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>