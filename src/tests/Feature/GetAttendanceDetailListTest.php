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

class GetAttendanceDetailListTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_勤怠詳細画面の「名前」がログインユーザーの氏名になっている()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 24, 10, 0));

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
            'clock_in' => Carbon::create(2026, 4, 24, 10, 0),
            'clock_out' => Carbon::create(2026, 4, 24, 19, 0),
        ]);

        $response = $this->get("/attendance/detail/{$attendance->id}");
        $response->assertStatus(200);
        $response->assertSee('test');
    }

    public function test_勤怠詳細画面の「日付」が選択した日付になっている()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 24, 10, 0));

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
            'clock_in' => Carbon::create(2026, 4, 24, 10, 0),
            'clock_out' => Carbon::create(2026, 4, 24, 19, 0),
        ]);

        $response = $this->get("/attendance/detail/{$attendance->id}");
        $response->assertSee('2026年');
        $response->assertSee('4月24日');
    }

    public function test_「出勤・退勤」にて記されている時間がログインユーザーの打刻と一致している()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 24, 10, 0));

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
            'clock_in' => Carbon::create(2026, 4, 24, 10, 0),
            'clock_out' => Carbon::create(2026, 4, 24, 19, 0),
        ]);

        $response = $this->get("/attendance/detail/{$attendance->id}");

        $response->assertSee('10:00'); // 出勤
        $response->assertSee('19:00'); // 退勤
    }
    
    public function test_「休憩」にて記されている時間がログインユーザーの打刻と一致している()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 24, 10, 0));

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
            'clock_in' => Carbon::create(2026, 4, 24, 10, 0),
            'clock_out' => Carbon::create(2026, 4, 24, 19, 0),
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => Carbon::create(2026, 4, 24, 12, 0),
            'end_time' => Carbon::create(2026, 4, 24, 13, 0),
        ]);

        $response = $this->get("/attendance/detail/{$attendance->id}");

        $response->assertSee('12:00');
        $response->assertSee('13:00');
    }
}
