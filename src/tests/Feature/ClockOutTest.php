<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Carbon\Carbon;

class ClockOutTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_退勤ボタンが正しく機能する()
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
        $response->assertSee('退勤');

        $this->post('/attendance/end')->assertRedirect('/attendance');
        $response = $this->get('/attendance');
        $response->assertSee('退勤済');

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'clock_out' => now()
        ]);

    }

    public function test_退勤時刻が勤怠一覧画面で確認できる()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 23, 10, 0));

        $user = User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->markEmailAsVerified();
        $this->actingAs($user);

        $this->post('/attendance')->assertRedirect('/attendance');

        Carbon::setTestNow(Carbon::create(2026, 4, 23, 11, 0));
        $this->post('/attendance/end')->assertRedirect('/attendance');

        $response = $this->get('/attendance/list');
        $response->assertSee('11:00');
    }
}
