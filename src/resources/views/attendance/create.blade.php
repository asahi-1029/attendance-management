<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/attendance/create.css') }}" />
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
        <div class="attendance">
            @if(session('error'))
                <div class="alert" style="color:red;">
                    {{ session('error') }}
                </div>
            @endif
            <div class="attendance__status">
                @if($status === 'attendance_before')
                    勤務外
                @elseif($status === 'working')
                    出勤中
                @elseif($status === 'break')
                    休憩中
                @elseif($status === 'attendance_after')
                    退勤済み
                @endif
            </div>
            <div class="attendance__date" id="current-date">
                {{ now()->isoFormat('YYYY年M月D日（ddd）') }}
            </div>
            <div class="attendance__time" id="current-time">
                {{ now()->format('H:i') }}
            </div>

            @if($status === 'attendance_before')
            <div class="attendance__button">
                <form action="/attendance" method="post">
                    @csrf
                    <button type="submit" class="attendance__button-start">
                        出勤
                    </button>
                </form>
            </div>

            @elseif($status === 'working')
            <div class="attendance__button-group">
                <form action="/attendance/end" method="post">
                    @csrf
                    <button type="submit" class="attendance__button-end">
                        退勤
                    </button>
                </form>
                <form action="/break/start" method="post">
                    @csrf
                    <button type="submit" class="attendance__button-braak-start">
                        休憩入
                    </button>
                </form>
            </div>

            @elseif($status === 'break')
            <div class="attendance__button">
                <form action="/break/end" method="post">
                    @csrf
                    <button type="submit" class="attendance__button-break-end">
                        休憩戻
                    </button>
                </form>
            </div>

            @elseif($status === 'attendance_after')
            <div class="attendance__message">
                お疲れ様でした。
            </div>
            @endif
        </div>
    </main>
    <script>
        function updateTime() {
            const now = new Date();

            // 日付
            const year = now.getFullYear();
            const month = now.getMonth() + 1;
            const day = now.getDate();

            const days = ["日", "月", "火", "水", "木", "金", "土"];
            const dayOfWeek = days[now.getDay()];

            const dateText = `${year}年${month}月${day}日（${dayOfWeek}）`;

            // 時刻
            let hours = now.getHours();
            let minutes = now.getMinutes();

            // 0埋め
            hours = String(hours).padStart(2, '0');
            minutes = String(minutes).padStart(2, '0');

            const timeText = `${hours}:${minutes}`;

            // 反映
            document.getElementById('current-date').textContent = dateText;
            document.getElementById('current-time').textContent = timeText;
        }

        // 初回実行
        updateTime();

        // 1秒ごとに更新
        setInterval(updateTime, 1000);
    </script>
</body>
</html>