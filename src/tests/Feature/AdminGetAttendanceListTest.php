<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Tests\TestCase;

class AdminGetAttendanceListTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_その日になされた全ユーザーの勤怠情報が正確に確認できる()
    {
        Carbon::setTestNow(Carbon::create(2026,4,27,10,0));

        $user1 = User::create([
            'name' => 'ユーザー1',
            'email' => 'user1@test.com',
            'password' => Hash::make('password'),
        ]);
        $user1->markEmailAsVerified();

        $user2 = User::create([
            'name' => 'ユーザー2',
            'email' => 'user2@test.com',
            'password' => Hash::make('password'),
        ]);
        $user2->markEmailAsVerified();

        $admin = User::create([
            'name' => '管理者',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'is_admin' => 1,
        ]);
        $admin->markEmailAsVerified();

        // 勤怠データ
        Attendance::create([
            'user_id' => $user1->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        Attendance::create([
            'user_id' => $user2->id,
            'date' => now()->toDateString(),
            'clock_in' => '10:00',
            'clock_out' => '19:00',
        ]);

        $this->actingAs($admin);
        $response = $this->get('/admin/attendance/list');

        $response->assertStatus(200);
        $response->assertSee('ユーザー1');
        $response->assertSee('ユーザー2');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('10:00');
        $response->assertSee('19:00');
    }

    public function test_遷移した際に現在の日付が表示される()
    {
        Carbon::setTestNow(Carbon::create(2026,4,27,10,0));

        $admin = User::create([
            'name' => '管理者',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'is_admin' => 1,
        ]);
        $admin->markEmailAsVerified();

        $this->actingAs($admin);
        $response = $this->get('/admin/attendance/list');
        $response->assertStatus(200);
        $response->assertSee('2026年4月27日');
    }

    public function test_「前日」を押下した時に前の日の勤怠情報が表示される()
    {
        Carbon::setTestNow(Carbon::create(2026,4,27,10,0));

        $user = User::create([
            'name' => 'ユーザー',
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
        ]);
        $user->markEmailAsVerified();

        $admin = User::create([
            'name' => '管理者',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'is_admin' => 1,
        ]);
        $admin->markEmailAsVerified();

        // 勤怠データ
        Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-04-26',
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        $this->actingAs($admin);
        $response = $this->get('/admin/attendance/list?date=2026-04-26');
        $response->assertStatus(200);
        $response->assertSee('ユーザー');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_「翌日」を押下した時に次の日の勤怠情報が表示される()
    {
        Carbon::setTestNow(Carbon::create(2026,4,27,10,0));

        $user = User::create([
            'name' => 'ユーザー',
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
        ]);
        $user->markEmailAsVerified();

        $admin = User::create([
            'name' => '管理者',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'is_admin' => 1,
        ]);
        $admin->markEmailAsVerified();

        // 勤怠データ
        Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-04-28',
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        $this->actingAs($admin);
        $response = $this->get('/admin/attendance/list?date=2026-04-28');
        $response->assertStatus(200);
        $response->assertSee('ユーザー');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }
}
