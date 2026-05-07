<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\StampCorrectionRequest;
use App\Models\StampCorrectionBreakTime;
use App\Http\Requests\StampCorrectionRequestRequest;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // 打刻画面
    public function create()
    {
        $today = now()->format('Y-m-d');

        $attendance = Attendance::where('user_id', auth()->id())
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            $status = 'attendance_before'; // まだ出勤してない
        } elseif ($attendance->clock_in && !$attendance->clock_out) {
            // 出勤してる

            $onBreak = $attendance->breakTimes()
                ->whereNull('end_time')
                ->exists();

            if ($onBreak) {
                $status = 'break'; // 休憩中
            } else {
                $status = 'working'; // 勤務中
            }
        } else {
            $status = 'attendance_after'; // 退勤済み
        }

        return view('attendance.create', compact('status'));
    }
    // 出勤
    public function clockIn()
    {
        $today = now()->format('Y-m-d');
        $attendance = Attendance::where('user_id', auth()->id())
            ->where('date', $today)
            ->first();
        
        if ($attendance) {
            // すでに出勤済み
            return back()->with('error', 'すでに出勤しています');
        }
        
        Attendance::create([
            'user_id' => auth()->id(),
            'date' => now()->format('Y-m-d'),
            'clock_in' => now(),
        ]);

        return redirect('/attendance');
    }

    // 休憩開始
    public function breakStart()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date', today())
            ->first();

        if ($attendance) {
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'start_time' => now(),
            ]);
        }

        return redirect('/attendance');
    }

    // 休憩終了
    public function breakEnd()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date', today())
            ->first();

        if ($attendance) {
            $break = BreakTime::where('attendance_id', $attendance->id)
                ->whereNull('end_time')
                ->first();

            if ($break) {
                $break->update([
                    'end_time' => now(),
                ]);
            }
        }

        return redirect('/attendance');
    }

    // 退勤
    public function clockOut()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('date', Carbon::today())
            ->first();

        if ($attendance && !$attendance->clock_out) {
            $attendance->update([
                'clock_out' => Carbon::now(),
            ]);
        }

        return redirect('/attendance');
    }

    // 勤怠一覧
    public function index(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));

        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        $attendances = Attendance::with('breakTimes')
            ->where('user_id', auth()->id())
            ->whereBetween('date', [$start, $end])
            ->get()
            ->keyBy(function ($item) {
                return $item->date->format('Y-m-d');
            });

        // 日付ループ
        $dates = [];
        $date = $start->copy();

        while ($date <= $end) {
            $dates[] = $date->copy();
            $date->addDay();
        }

        return view('attendance.index', compact('dates', 'month', 'attendances'));
    }

    public function show($id)
    {
        $attendance = Attendance::with('breakTimes', 'user')->findOrFail($id);

        $correction = StampCorrectionRequest::with('stampCorrectionBreakTimes')
            ->where('attendance_id', $id)
            ->latest()
            ->first();

        return view('attendance.show', compact('attendance', 'correction'));
    }

    public function update(StampCorrectionRequestRequest $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $date = $attendance->date->format('Y-m-d');

        // 修正申請 作成
        $correction = StampCorrectionRequest::create([
            'attendance_id' => $attendance->id,
            'user_id' => auth()->id(),
            'clock_in' => Carbon::parse($date . ' ' . $request->clock_in),
            'clock_out' => Carbon::parse($date . ' ' . $request->clock_out),
            'note' => $request->note,
        ]);

        // 休憩も保存
        if ($request->breaks) {
            foreach ($request->breaks as $break) {
                if (!empty($break['start_time']) && !empty($break['end_time'])) {
                    StampCorrectionBreakTime::create([
                        'stamp_correction_request_id' => $correction->id,
                        'start_time' => Carbon::parse($date . ' ' . $break['start_time']),
                        'end_time' => Carbon::parse($date . ' ' . $break['end_time']),
                    ]);
                }
            }
        }

        return redirect('/attendance/list');
    }
}