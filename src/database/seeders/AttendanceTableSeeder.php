<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\BreakTime;

class AttendanceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($date = '2026-01-01'; $date <= '2026-03-31'; $date = date('Y-m-d', strtotime($date . ' +1 day'))){
            
            $attendance = Attendance::create([
                'user_id' => 2,
                'date' => $date,
                'clock_in' => $date . ' 09:00:00',
                'clock_out' => $date . ' 18:00:00',
                'note' => 'aaa',
            ]);

            BreakTime::create([
                'attendance_id' => $attendance->id,
                'start_time' => $date . ' 12:00:00',
                'end_time' => $date . ' 13:00:00',
            ]);

        }
    }
}
