<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/admin/attendance/show.css') }}" />
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
            <form action="/admin/attendance/{{ $attendance->id }}" method="post">
            @csrf
                <div class="attendance-detail__list">
                    <div class="attendance-detail__row">
                        <div class="attendance-detail__label">名前</div>
                        <div class="attendance-detail__value">{{ $attendance->user->name }}</div>
                    </div>
                    <div class="attendance-detail__row">
                        <div class="attendance-detail__label">日付</div>
                        <div class="attendance-detail__value attendance-detail__value--date">
                            <div class="date-column">
                                <span>{{ $attendance->date->format('Y年') }}</span>
                            </div>
                            <div class="date-column">
                                <span>{{ $attendance->date->format('n月j日') }}</span>
                            </div>
                        </div>
                    </div>
                    @php
                        $isPending = $correction && $correction->status === 'pending';
                        $correctionBreaks = $correction?->stampCorrectionBreakTimes ?? collect();
                    @endphp
                    <div class="attendance-detail__row">
                        <div class="attendance-detail__label">出勤・退勤</div>
                        <div class="{{ $isPending ? 'attendance-detail__value--view' : 'attendance-detail__value' }}">
                            @if($isPending)
                                <span>{{ $correction->clock_in?->format('H:i') }}</span>
                                <span>～</span>
                                <span>{{ $correction->clock_out?->format('H:i') }}</span>
                            @else
                                <div class="input-wrap">
                                    <input type="text" name="clock_in" value="{{ old('clock_in', $attendance->clock_in?->format('H:i')) }}">
                                    <div class="form__error">
                                        @error('clock_in') 
                                        {{ $message }} 
                                        @enderror
                                    </div>
                                </div>
                                <span>～</span>
                                <div class="input-wrap">
                                    <input type="text" name="clock_out" value="{{ old('clock_out', $attendance->clock_out?->format('H:i')) }}">
                                    <div class="form__error">
                                        @error('clock_out')
                                        {{ $message }} 
                                        @enderror
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @if($isPending)
                        @foreach($correctionBreaks as $index => $cBreak)
                        <div class="attendance-detail__row">
                            <div class="attendance-detail__label">休憩{{ $index + 1 }}</div>
                            <div class="{{ $isPending ? 'attendance-detail__value--view' : 'attendance-detail__value' }}">
                                <span>{{ $cBreak?->start_time?->format('H:i') }}</span>
                                <span>～</span>
                                <span>{{ $cBreak?->end_time?->format('H:i') }}</span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        @for($i = 0; $i < 2; $i++)
                            @php
                                $break = $attendance->breakTimes[$i] ?? new App\Models\BreakTime();
                            @endphp
                            <div class="attendance-detail__row">
                                <div class="attendance-detail__label">休憩{{ $i + 1 }}</div>
                                <div class="{{ $isPending ? 'attendance-detail__value--view' : 'attendance-detail__value' }}">
                                    <div class="input-wrap">
                                        <input type="text" name="breaks[{{ $i }}][start_time]" 
                                            value="{{ old('breaks.'.$i.'.start_time', $break->start_time?->format('H:i')) }}">
                                        <div class="form__error">
                                            @error('breaks.'.$i.'.start_time')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <span>～</span>
                                    <div class="input-wrap">
                                        <input type="text" name="breaks[{{ $i }}][end_time]" 
                                            value="{{ old('breaks.'.$i.'.end_time', $break->end_time?->format('H:i')) }}">
                                        <div class="form__error">
                                            @error('breaks.'.$i.'.end_time')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    @endif
                    <div class="attendance-detail__row">
                        <div class="attendance-detail__label">備考</div>
                        <div class="attendance-detail__value">
                            @if($isPending)
                                <div>{{ $correction?->note }}</div>
                            @else
                                <textarea name="note" >{{ old('note', $attendance?->note) }}</textarea>
                            @endif
                            <div class="form__error">
                                @error('note')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                @if($correction && $correction->status === 'pending')
                    <div class="error">*承認待ちのため修正はできません</div>
                @else
                    <div class="form__button">
                        <button class="form__button-update" type="submit">修正</button>
                    </div>
                @endif
            </form>
        </div>
    </main>
</body>
</html>