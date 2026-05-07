<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StampCorrectionRequest;

class AdminStampCorrectionRequestController extends Controller
{
    public function show($attendance_correct_request_id)
    {
        $correction = StampCorrectionRequest::with('user','attendance','stampCorrectionBreakTimes')
            ->findOrFail($attendance_correct_request_id);

        return view('admin.stamp_correction_request.show',compact('correction'));
    }

    public function admit($attendance_correct_request_id)
    {
        $correction = StampCorrectionRequest::with('attendance','stampCorrectionBreakTimes')
            ->findOrfail($attendance_correct_request_id);

        $correction->attendance->update([
            'clock_in' => $correction->clock_in,
            'clock_out' => $correction->clock_out,
            'note' => $correction->note,
        ]);

        $correction->attendance->breakTimes()->delete();

        foreach ($correction->stampCorrectionBreakTimes as $break) {
            $correction->attendance->breakTimes()->create([
                'start_time' => $break->start_time,
                'end_time' => $break->end_time,
            ]);
        }

        $correction->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return back();
    }
}
