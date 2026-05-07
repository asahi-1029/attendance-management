<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Carbon\Carbon;

class BreakTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_休憩ボタンが正しく機能する()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 23, 10, 0));

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
            'clock_in' => now(),
            'clock_out' => null,
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('休憩入');

        $this->post('/break/start')->assertRedirect('/attendance');

        // DB確認
        $this->assertDatabaseHas('break_times', [
            'attendance_id' => $attendance->id,
            'start_time' => now(),
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
    }

    public function test_休憩は一日に何回でもできる()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 23, 10, 0));

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
            'clock_in' => now(),
            'clock_out' => null,
        ]);

        //1回目
        $this->post('/break/start')->assertRedirect('/attendance');
        $this->post('/break/end')->assertRedirect('/attendance');

        // 2回目
        $this->post('/break/start')->assertRedirect('/attendance');
        $this->post('/break/end')->assertRedirect('/attendance');

        $this->assertDatabaseCount('break_times', 2);

        $response = $this->get('/attendance');
        $response->assertSee('休憩入');
    }

    public function test_休憩戻ボタンが正しく機能する()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 23, 10, 0));

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
            'clock_in' => now(),
            'clock_out' => null,
        ]);

        $this->post('/break/start')->assertRedirect('/attendance');
        $response = $this->get('/attendance');
        $response->assertSee('休憩戻');

        // DB確認（ここ重要）
        $this->assertDatabaseHas('break_times', [
            'attendance_id' => $attendance->id,
            'start_time' => now(),
            'end_time' => null,
        ]);

        $this->post('/break/end')->assertRedirect('/attendance');
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
    }
    
    public function test_休憩戻は一日に何回でもできる()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 23, 10, 0));

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
            'clock_in' => now(),
            'clock_out' => null,
        ]);

        //1回目
        $this->post('/break/start')->assertRedirect('/attendance');
        $this->post('/break/end')->assertRedirect('/attendance');

        // 2回目
        $this->post('/break/start')->assertRedirect('/attendance');
        $response = $this->get('/attendance');
        $response->assertSee('休憩戻');
    }

    public function test_休憩時刻が勤怠一覧画面で確認できる()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 23, 10, 0));

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
            'clock_in' => now(),
            'clock_out' => null,
        ]);

        $this->post('/break/start')->assertRedirect('/attendance');

        Carbon::setTestNow(Carbon::create(2026, 4, 23, 11, 0));
        $this->post('/break/end')->assertRedirect('/attendance');

        $response = $this->get('/attendance/list');
        $response->assertSee('1:00');
    }
}
