<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Tests\TestCase;

class AdminGetUpdateAttendanceListTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_勤怠詳細画面に表示されるデータが選択したものになっている()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 27, 10, 0));

        $user = User::create([
            'name' => 'ユーザー',
            'email' => 'user@test.com',
            'password' => Hash::make('password')
        ]);
        $user->markEmailAsVerified();

        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => 1
        ]);
        $admin->markEmailAsVerified();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-04-27',
            'clock_in' => '10:00',
            'clock_out' => '19:00',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => '12:00',
            'end_time' => '13:00',
        ]);

        $this->actingAs($admin);
        $response = $this->get("/admin/attendance/{$attendance->id}");
        $response->assertStatus(200);

        $response->assertSee('ユーザー');   // 名前
        $response->assertSee('2026年');
        $response->assertSee('4月27日');
        $response->assertSee('10:00');       // 出勤
        $response->assertSee('19:00');       // 退勤
        $response->assertSee('12:00');       // 休憩開始
        $response->assertSee('13:00');       // 休憩終了
    }

    public function test_出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 27, 10, 0));

        $user = User::create([
            'name' => 'ユーザー',
            'email' => 'user@test.com',
            'password' => Hash::make('password')
        ]);
        $user->markEmailAsVerified();

        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => 1
        ]);
        $admin->markEmailAsVerified();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => Carbon::create(2026, 4, 27, 10, 0),
            'clock_out' => Carbon::create(2026, 4, 27, 19, 0),
        ]);

        $this->actingAs($admin);
        $response = $this->get("/admin/attendance/{$attendance->id}");
        $response->assertStatus(200);

        $response = $this->post("/admin/attendance/{$attendance->id}", [
            'clock_in' => Carbon::create(2026, 4, 27, 19, 0)->format('H:i'),
            'clock_out' => Carbon::create(2026, 4, 27, 10, 0)->format('H:i'),
            'note' => 'テスト',
        ]);

        $response->assertSessionHasErrors([
            'clock_out' => '出勤時間もしくは退勤時間が不適切な値です'
        ]);

    }

    public function test_休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 27, 10, 0));

        $user = User::create([
            'name' => 'ユーザー',
            'email' => 'user@test.com',
            'password' => Hash::make('password')
        ]);
        $user->markEmailAsVerified();

        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => 1
        ]);
        $admin->markEmailAsVerified();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => Carbon::create(2026, 4, 27, 10, 0),
            'clock_out' => Carbon::create(2026, 4, 27, 19, 0),
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => Carbon::create(2026, 4, 27, 12, 0),
            'end_time' => Carbon::create(2026, 4, 27, 13, 0),
        ]);

        $this->actingAs($admin);
        $response = $this->get("/admin/attendance/{$attendance->id}");
        $response->assertStatus(200);

        $response = $this->post("/admin/attendance/{$attendance->id}", [
            'clock_in' => Carbon::create(2026, 4, 27, 10, 0)->format('H:i'),
            'clock_out' => Carbon::create(2026, 4, 27, 19, 0)->format('H:i'),
            'note' => 'テスト',
            'breaks' => [
                [
                    'start_time' => '19:30',
                    'end_time' => '13:00',
                ]
            ]
        ]);

        $response->assertSessionHasErrors([
            'breaks.0.start_time' => '休憩時間が不適切な値です'
        ]);
    }

    public function test_休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 27, 10, 0));

        $user = User::create([
            'name' => 'ユーザー',
            'email' => 'user@test.com',
            'password' => Hash::make('password')
        ]);
        $user->markEmailAsVerified();

        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => 1
        ]);
        $admin->markEmailAsVerified();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => Carbon::create(2026, 4, 27, 10, 0),
            'clock_out' => Carbon::create(2026, 4, 27, 19, 0),
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => Carbon::create(2026, 4, 27, 12, 0),
            'end_time' => Carbon::create(2026, 4, 27, 13, 0),
        ]);

        $this->actingAs($admin);
        $response = $this->get("/admin/attendance/{$attendance->id}");
        $response->assertStatus(200);

        $response = $this->post("/admin/attendance/{$attendance->id}", [
            'clock_in' => Carbon::create(2026, 4, 27, 10, 0)->format('H:i'),
            'clock_out' => Carbon::create(2026, 4, 27, 19, 0)->format('H:i'),
            'note' => 'テスト',
            'breaks' => [
                [
                    'start_time' => '12:00',
                    'end_time' => '19:30',
                ]
            ]
        ]);

        $response->assertSessionHasErrors([
            'breaks.0.end_time' => '休憩時間もしくは退勤時間が不適切な値です'
        ]);
    }

    public function test_備考欄が未入力の場合のエラーメッセージが表示される()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 27, 10, 0));

        $user = User::create([
            'name' => 'ユーザー',
            'email' => 'user@test.com',
            'password' => Hash::make('password')
        ]);
        $user->markEmailAsVerified();

        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => 1
        ]);
        $admin->markEmailAsVerified();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => Carbon::create(2026, 4, 27, 10, 0),
            'clock_out' => Carbon::create(2026, 4, 27, 19, 0),
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_time' => Carbon::create(2026, 4, 27, 12, 0),
            'end_time' => Carbon::create(2026, 4, 27, 13, 0),
        ]);

        $this->actingAs($admin);
        $response = $this->get("/admin/attendance/{$attendance->id}");
        $response->assertStatus(200);

        $response = $this->post("/admin/attendance/{$attendance->id}", [
            'clock_in' => Carbon::create(2026, 4, 27, 10, 0)->format('H:i'),
            'clock_out' => Carbon::create(2026, 4, 27, 19, 0)->format('H:i'),
            'note' => '',
            'breaks' => [
                [
                    'start_time' => '12:30',
                    'end_time' => '13:30',
                ]
            ]
        ]);

        $response->assertSessionHasErrors([
            'note' => '備考を記入してください'
        ]);
    }
}
