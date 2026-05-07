<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Carbon\Carbon;

class GetAttendanceListTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_自分が行った勤怠情報が全て表示されている()
    {
        $clockIn = Carbon::create(2026, 4, 23, 10, 0);
        $clockOut = Carbon::create(2026, 4, 23, 13, 0);
        $startTime = Carbon::create(2026, 4, 23, 11, 0);
        $endTime = Carbon::create(2026, 4, 23, 12, 0);

        $user = User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->markEmailAsVerified();
        $this->actingAs($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => $startTime,
            'end_time' => $endTime
        ]);

        $response = $this->get('/attendance/list');
        $response->assertSee('04/23');
        $response->assertSee('10:00');
        $response->assertSee('13:00');
        $response->assertSee('1:00');
        //合計時間
        $response->assertSee('2:00');
    }

    public function test_勤怠一覧画面に遷移した際に現在の月が表示される()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 23));

        $user = User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->markEmailAsVerified();
        $this->actingAs($user);

        $response = $this->get('/attendance/list');

        $response->assertStatus(200);

        $response->assertSee('2026-04');
    }

    public function test_「前月」を押下した時に表示月の前月の情報が表示される()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 23));

        $user = User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->markEmailAsVerified();
        $this->actingAs($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-03-23',
            'clock_in' => Carbon::create(2026, 3, 23, 10, 0),
            'clock_out' => Carbon::create(2026, 3, 23, 13, 0),
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => Carbon::create(2026, 3, 23, 11, 0),
            'end_time' => Carbon::create(2026, 3, 23, 12, 0)
        ]);

        $response = $this->get('/attendance/list?month=2026-03');
        $response->assertStatus(200);
        $response->assertSee('03/23');
        $response->assertSee('10:00'); // 出勤
        $response->assertSee('13:00'); // 退勤
        $response->assertSee('1:00');
        //合計時間
        $response->assertSee('2:00');
    }

    public function test_「翌月」を押下した時に表示月の翌月の情報が表示される()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 23));

        $user = User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->markEmailAsVerified();
        $this->actingAs($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-05-23',
            'clock_in' => Carbon::create(2026, 5, 23, 10, 0),
            'clock_out' => Carbon::create(2026, 5, 23, 13, 0),
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => Carbon::create(2026, 5, 23, 11, 0),
            'end_time' => Carbon::create(2026, 5, 23, 12, 0)
        ]);

        $response = $this->get('/attendance/list?month=2026-05');
        $response->assertStatus(200);
        $response->assertSee('05/23');
        $response->assertSee('10:00'); // 出勤
        $response->assertSee('13:00'); // 退勤
        $response->assertSee('1:00');
        //合計時間
        $response->assertSee('2:00');
    }

    public function test_「詳細」を押下すると、その日の勤怠詳細画面に遷移する()
    {
        $clockIn = Carbon::create(2026, 4, 23, 10, 0);
        $clockOut = Carbon::create(2026, 4, 23, 13, 0);
        $startTime = Carbon::create(2026, 4, 23, 11, 0);
        $endTime = Carbon::create(2026, 4, 23, 12, 0);

        $user = User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->markEmailAsVerified();
        $this->actingAs($user);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-04-23',
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => $startTime,
            'end_time' => $endTime
        ]);

        $response = $this->get("/attendance/detail/{$attendance->id}");
        $response->assertStatus(200);
    }
}
