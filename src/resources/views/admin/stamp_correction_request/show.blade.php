<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin/stamp_correction_request/show.css') }}" />
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
        <div class="attendance-detail">
            <h1 class="attendance-detail__title">
                <span class="title__bar"></span>
                勤怠詳細
            </h1>
            <form action="/stamp_correction_request/approve/{{ $correction->id }}" method="post">
                @csrf
                <div class="attendance-detail__list">
                    <div class="attendance-detail__row">
                        <div class="attendance-detail__label">名前</div>
                        <div class="attendance-detail__value">{{ $correction->user->name }}</div>
                    </div>
                    <div class="attendance-detail__row">
                        <div class="attendance-detail__label">日付</div>
                        <div class="attendance-detail__value attendance-detail__value--date">
                            <div class="date-column">
                                <span>{{ $correction->attendance->date->format('Y年') }}</span>
                            </div>
                            <div class="date-column">
                                <span>{{ $correction->attendance->date->format('n月j日') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="attendance-detail__row">
                        <div class="attendance-detail__label">出勤・退勤</div>
                        <div class="attendance-detail__value">
                            <span>{{ $correction->clock_in->format('H:i') }}</span>
                            <span>～</span>
                            <span>{{ $correction->clock_out->format('H:i') }}</span>
                        </div>
                    </div>
                    @for ($i = 0; $i < 2; $i++)
                        @php
                            $break = $correction->stampCorrectionBreakTimes[$i] ?? null;
                        @endphp
                        <div class="attendance-detail__row">
                            <div class="attendance-detail__label">休憩{{ $i + 1 }}</div>
                            <div class="attendance-detail__value">
                                @if ($break?->start_time && $break?->end_time)
                                    <span>{{ $break?->start_time?->format('H:i') }}</span>
                                    <span>～</span>
                                    <span>{{ $break?->end_time?->format('H:i') }}</span>
                                @endif
                            </div>
                        </div>
                    @endfor
                    <div class="attendance-detail__row">
                        <div class="attendance-detail__label">備考</div>
                        <div class="attendance-detail__value">
                            <p>{{ $correction->note }}</p>
                        </div>
                    </div>
                </div>
                @if ($correction->status === 'pending')
                    <div class="form__button">
                        <button class="form__button-admit" type="submit">承認</button>
                    </div>
                @else
                    <div class="form__button">
                         <div class="form__button-admitted">承認済み</div>
                    </div>
                @endif
            </form>
        </div>
    </main>
</body>
</html>