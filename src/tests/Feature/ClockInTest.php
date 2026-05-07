<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ClockInTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_出勤ボタンが正しく機能する()
    {
        $user = User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->markEmailAsVerified();

        $this->actingAs($user);

        $response = $this->get('/attendance');
        $response->assertSee('出勤');

        //出勤ボタン押下
        $postResponse = $this->post('/attendance');
        $postResponse->assertRedirect('/attendance');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'clock_out' => null,
        ]);
        
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
    }

    public function test_出勤は一日一回のみできる()
    {
        $user = User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->markEmailAsVerified();

        $this->actingAs($user);

        Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => now()->subHour(),
            'clock_out' => now(),
        ]);

        $response = $this->get('/attendance');
        $response->assertDontSee('出勤');
    }

    public function test_出勤時刻が勤怠一覧画面で確認できる()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 22, 9, 0));

        $user = User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->markEmailAsVerified();

        $this->actingAs($user);

        $this->post('/attendance');

        $response = $this->get('/attendance/list');
        $response->assertSee('09:00');
    }
}
