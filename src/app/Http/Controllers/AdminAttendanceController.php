<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use App\Http\Requests\StampCorrectionRequestRequest;
use Carbon\Carbon;

class AdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = Carbon::parse($request->input('date', now()));
        $attendances = Attendance::with('breakTimes','user')
            ->whereDate('date', $date)
            ->get();
        return view('admin.attendance.index', compact('attendances', 'date'));
    }

    public function staff(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $month = $request->input('month',now()->format('Y-m'));

        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        $attendances = Attendance::with('breakTimes')
            ->where('user_id', $id)
            ->whereBetween('date',[$start, $end])
            ->get()
            ->keyBy(function ($item) {
                return $item->date->format('Y-m-d');
            });
        
        $dates = [];
        $date = $start->copy();

        while ($date <= $end) {
            $dates[] = $date->copy();
            $date->addDay();
        }

        return view('admin.attendance.staff', compact('user','dates', 'month', 'attendances'));
    }

    public function show($id)
    {
        $attendance = Attendance::with('breakTimes','user')
            ->findOrFail($id);
        
        $correction = StampCorrectionRequest::with('stampCorrectionBreakTimes')
            ->where('attendance_id', $id)
            ->latest()
            ->first();
        
        return view('admin.attendance.show', compact('attendance','correction'));
    }

    public function update(StampCorrectionRequestRequest $request,$id)
    {
        $attendance = Attendance::findOrFail($id);
        
        $date = $attendance->date->format('Y-m-d');

        $attendance->update([
            'clock_in' => Carbon::parse($date . ' ' . $request->clock_in),
            'clock_out' => Carbon::parse($date . ' ' . $request->clock_out),
            'note' => $request->note,
        ]);

        if ($request->breaks) {
            foreach ($request->breaks as $i => $break) {
                if (!empty($break['start_time']) && !empty($break['end_time'])) {
                    $attendance->breakTimes[$i]?->update([
                        'start_time' => Carbon::parse($date . ' ' . $break['start_time']),
                        'end_time' => Carbon::parse($date . ' ' . $break['end_time']),
                    ]);
                }
            }
        }

        return redirect("/admin/attendance/staff/{$attendance->user_id}");

    }

    public function export(Request $request)
    {
        $userId = $request->user_id;
        $month = $request->month ?? now()->format('Y-m');

        // 文字列を日付オブジェクトとして使えるようにする
        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        $attendances = Attendance::with('breakTimes', 'user')
            ->where('user_id', $userId)
            ->whereBetween('date', [$start,$end])
            ->get();
        
        return response()->streamDownload(function () use ($attendances) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            //CSVヘッダー
            fputcsv($handle, ['名前','日付','出勤','退勤','休憩','合計']);

            foreach ($attendances as $attendance) {
                fputcsv($handle, [
                    $attendance->user->name,
                    $attendance->date?->format('Y-m-d'),
                    $attendance->clock_in?->format('H:i'),
                    $attendance->clock_out?->format('H:i'),
                    $attendance->break_time_formatted,
                    $attendance->work_time_formatted,
                ]);
            }

            fclose($handle);
        }, 'attendance.csv');
    }
}
