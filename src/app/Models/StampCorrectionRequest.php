<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StampCorrectionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_id',
        'clock_in',
        'clock_out',
        'note',
        'status',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function stampCorrectionBreakTimes()
    {
        return $this->hasMany(StampCorrectionBreakTime::class);
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => '承認待ち',
            'approved' => '承認済み',
        };
    }
}
