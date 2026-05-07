<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Tests\TestCase;

class AdminGetUserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_管理者ユーザーが全ユーザーの「氏名」「メールアドレス」を確認できる()
    {
        $user1 = User::create([
            'name' => 'ユーザー1',
            'email' => 'user1@test.com',
            'password' => Hash::make('password')
        ]);
        $user1->markEmailAsVerified();

        $user2 = User::create([
            'name' => 'ユーザー2',
            'email' => 'user2@test.com',
            'password' => Hash::make('password')
        ]);
        $user2->markEmailAsVerified();

        $admin = User::create([
            'name' => '管理者',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'is_admin' => 1,
        ]);
        $admin->markEmailAsVerified();

        $this->actingAs($admin);
        $response = $this->get('/admin/staff/list');
        $response->assertStatus(200);
        $response->assertSee('ユーザー1');
        $response->assertSee('user1@test.com');
        $response->assertSee('ユーザー2');
        $response->assertSee('user2@test.com');
    }

    public function test_ユーザーの勤怠情報が正しく表示される()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 27, 10, 0));

        $user = User::create([
            'name' => 'ユーザー',
            'email' => 'user@test.com',
            'password' => Hash::make('password')
        ]);
        $user->markEmailAsVerified();

        $admin = User::create([
            'name' => '管理者',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'is_admin' => 1,
        ]);
        $admin->markEmailAsVerified();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-04-27',
            'clock_in' => '10:00',
            'clock_out' => '19:00',
        ]);

        $this->actingAs($admin);
        $response = $this->get("/admin/attendance/list");
        $response->assertStatus(200);
        $response->assertSee('2026年4月27日');
        $response->assertSee('ユーザー');
        $response->assertSee('10:00');
        $response->assertSee('19:00');
    }

    public function test_「前日」を押下した時に表示日の前日の情報が表示される()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 27, 10, 0));

        $user = User::create([
            'name' => 'ユーザー',
            'email' => 'user@test.com',
            'password' => Hash::make('password')
        ]);
        $user->markEmailAsVerified();

        $admin = User::create([
            'name' => '管理者',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'is_admin' => 1,
        ]);
        $admin->markEmailAsVerified();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-04-26',
            'clock_in' => '10:00',
            'clock_out' => '19:00',
        ]);

        $this->actingAs($admin)
            ->get('/admin/attendance/list')
            ->assertSee('2026年4月27日');

        $response = $this->get('/admin/attendance/list?date=2026-04-26');
        $response->assertStatus(200);
        $response->assertSee('2026年4月26日');
        $response->assertSee('ユーザー');
        $response->assertSee('10:00');
        $response->assertSee('19:00');
    }

    public function test_「翌日」を押下した時に表示日の翌日の情報が表示される()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 27, 10, 0));

        $user = User::create([
            'name' => 'ユーザー',
            'email' => 'user@test.com',
            'password' => Hash::make('password')
        ]);
        $user->markEmailAsVerified();

        $admin = User::create([
            'name' => '管理者',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'is_admin' => 1,
        ]);
        $admin->markEmailAsVerified();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-04-28',
            'clock_in' => '10:00',
            'clock_out' => '19:00',
        ]);

        $this->actingAs($admin)
            ->get('/admin/attendance/list')
            ->assertSee('2026年4月27日');
        
        $response = $this->get('/admin/attendance/list?date=2026-04-28');
        $response->assertStatus(200);
        $response->assertSee('2026年4月28日');
        $response->assertSee('ユーザー');
        $response->assertSee('10:00');
        $response->assertSee('19:00');
    }

    public function test_「詳細」を押下すると、その日の勤怠詳細画面に遷移する()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 27, 10, 0));

        $user = User::create([
            'name' => 'ユーザー',
            'email' => 'user@test.com',
            'password' => Hash::make('password')
        ]);
        $user->markEmailAsVerified();

        $admin = User::create([
            'name' => '管理者',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'is_admin' => 1,
        ]);
        $admin->markEmailAsVerified();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-04-27',
            'clock_in' => '10:00',
            'clock_out' => '19:00',
        ]);

        $this->actingAs($admin);
        $response = $this->get('/admin/attendance/list');
        $response->assertSee("/admin/attendance/{$attendance->id}");
        $detailResponse = $this->get("/admin/attendance/{$attendance->id}");
        $detailResponse->assertStatus(200);

        $detailResponse->assertSee('ユーザー');
        $detailResponse->assertSee('2026年');
        $detailResponse->assertSee('4月27日');
        $detailResponse->assertSee('10:00');
        $detailResponse->assertSee('19:00');
    }
}
