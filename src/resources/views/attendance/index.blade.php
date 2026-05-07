<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/attendance/index.css') }}" />
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
            <h1 class="attendance-list__title">
                <span class="title__bar"></span>
                勤怠一覧
            </h1>
            <div class="attendance-list">
                <div class="attendance-list__month">
                    <button type="button" id="prevMonth" class="month__btn">← 前月</button>
                    <input type="month" id="monthPicker" class="month__current" value="{{ $month }}">
                    <button type="button" id="nextMonth" class="month__btn">翌月 →</button>
                </div>
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th>日付</th>
                            <th>出勤</th>
                            <th>退勤</th>
                            <th>休憩</th>
                            <th>合計</th>
                            <th>詳細</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dates as $date)
                            @php
                                $attendance = $attendances[$date->format('Y-m-d')] ?? null;
                            @endphp
                            <tr>
                                <!-- 日付 -->
                                <td>{{ $date->isoFormat('MM/DD(ddd)') }}</td>

                                <!-- 出勤 -->
                                <td>{{ $attendance?->clock_in?->format('H:i') }}</td>

                                <!-- 退勤 -->
                                <td>{{ $attendance?->clock_out?->format('H:i') }}</td>

                                <!-- 休憩 -->
                                <td>{{ $attendance?->break_time_formatted }}</td>

                                <!-- 合計 -->
                                <td>{{ $attendance?->work_time_formatted }}</td>

                                <!-- 詳細 -->
                                <td>
                                    @if ($attendance)
                                        <a href="/attendance/detail/{{ $attendance->id }}">詳細</a>
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
        const monthPicker = document.getElementById('monthPicker');

        // 月変更したらリロード
        monthPicker.addEventListener('change', () => {
            location.href = `/attendance/list?month=${monthPicker.value}`;
        });

        // 前月
        document.getElementById('prevMonth').addEventListener('click', () => {
            const date = new Date(monthPicker.value);
            date.setMonth(date.getMonth() - 1);
            updateAndReload(date);
        });

        // 翌月
        document.getElementById('nextMonth').addEventListener('click', () => {
            const date = new Date(monthPicker.value);
            date.setMonth(date.getMonth() + 1);
            updateAndReload(date);
        });

        function updateAndReload(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const newMonth = `${y}-${m}`;

            location.href = `/attendance/list?month=${newMonth}`;
        }
    </script>
</body>
</html>