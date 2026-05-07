<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime'
    ];

    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function stampCorrectionRequests()
    {
        return $this->hasMany(StampCorrectionRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //休憩時間
    public function getBreakTotalMinutesAttribute()
    {
        $total = 0;

        foreach ($this->breakTimes as $break) {
            if ($break->start_time && $break->end_time) {
                $total += $break->start_time->diffInMinutes($break->end_time);
            }
        }

        return $total;
    }

    //休憩時間表示用
    public function getBreakTimeFormattedAttribute()
    {
        $minutes = $this->break_total_minutes;

        $h = floor($minutes / 60);
        $m = $minutes % 60;

        return sprintf('%d:%02d', $h, $m);
    }

    //勤務時間
    public function getWorkMinutesAttribute()
    {
        if (!$this->clock_in || !$this->clock_out) {
            return 0;
        }

        return $this->clock_in->diffInMinutes($this->clock_out)
            - $this->break_total_minutes;
    }

    //勤務時間表示用
    public function getWorkTimeFormattedAttribute()
    {
        $minutes = $this->work_minutes;

        $h = floor($minutes / 60);
        $m = $minutes % 60;

        return sprintf('%d:%02d', $h, $m);
    }
}
