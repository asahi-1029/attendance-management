<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin/attendance/staff.css') }}" />
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
                {{ $user->name }}さんの勤怠
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
                                        <a href="/admin/attendance/{{ $attendance->id }}">詳細</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <form action="{{'/export?' . http_build_query(array_merge(request()->query(), ['user_id' => $user->id])) }}" method="post">
                @csrf
                <div class="form__button">
                    <button class="form__button-csv" type="submit">CSV出力</button>
                </div>
            </form>
        </div>
    </main>
    <script>
        const monthPicker = document.getElementById('monthPicker');

        monthPicker.addEventListener('change', () => {
            location.href = `/admin/attendance/staff/{{ $user->id }}?month=${monthPicker.value}`;
        });

        //前月
        document.getElementById('prevMonth').addEventListener('click',() => {
            const date = new Date(monthPicker.value);
            date.setMonth(date.getMonth() - 1);
            updateMonth(date);
        });

        //翌月
        document.getElementById('nextMonth').addEventListener('click',() => {
            const date = new Date(monthPicker.value);
            date.setMonth(date.getMonth() + 1);
            updateMonth(date);
        });

        function updateMonth(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const newMonth = `${y}-${m}`;

           window.location.href = `/admin/attendance/staff/{{ $user->id }}?month=${newMonth}`;
        }
    </script>
</body>
</html>