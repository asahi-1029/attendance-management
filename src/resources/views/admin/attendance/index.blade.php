<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin/attendance/index.css') }}" />
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <img class="header__logo" src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH">
            <nav>
                <ul class="header__nav">
                    <li><a href="/admin/attendance/list">勤怠一覧</a></li>
                    <li><a href="/admin/staff/list">スタッフ一覧</a></li>
                    <li><a href="/stamp_correction_request/list">申請一覧</a></li>
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
            <h1 class="attendance-list__title">
                <span class="title__bar"></span>
                {{ $date->isoFormat('YYYY年M月D日') }}の勤怠
            </h1>
            <div class="attendance-list">
                <div class="attendance-list__month">
                    <button type="button" id="prevDate" class="date__btn">← 前日</button>
                    <input type="date" id="datePicker" class="date__current">
                    <button type="button" id="nextDate" class="date__btn">翌日 →</button>
                </div>
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th>名前</th>
                            <th>出勤</th>
                            <th>退勤</th>
                            <th>休憩</th>
                            <th>合計</th>
                            <th>詳細</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attendances as $attendance)
                            <tr>
                                <td>{{ $attendance?->user?->name }}</td>
                                <td>{{ $attendance?->clock_in?->format('H:i') }}</td>
                                <td>{{ $attendance?->clock_out?->format('H:i') }}</td>
                                <td>{{ $attendance?->break_time_formatted }}</td>
                                <td>{{ $attendance?->work_time_formatted }}</td>
                                <td>
                                    @if ($attendance)
                                        <a href="/admin/attendance/{{ $attendance->id }}">詳細</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script>
        const datePicker = document.getElementById('datePicker');

        // 初期値（今日）
        datePicker.value = "{{ $date->format('Y-m-d') }}";

        // 直接変更
        datePicker.addEventListener('change', () => {
            location.href = `/admin/attendance/list?date=${datePicker.value}`;
        });
        // 前日
        document.getElementById('prevDate').addEventListener('click', () => {
            const date = new Date(datePicker.value);
            date.setDate(date.getDate() - 1);
            updateDate(date);
        });

        // 翌日
        document.getElementById('nextDate').addEventListener('click', () => {
            const date = new Date(datePicker.value);
            date.setDate(date.getDate() + 1);
            updateDate(date);
        });

        function updateDate(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            
            const newDate = `${y}-${m}-${d}`;
            location.href = `/admin/attendance/list?date=${newDate}`;
        }
    </script>
</body>
</html>