<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUpdateAttendanceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_承認待ちの修正申請が全て表示されている()
    {
        $admin = User::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => 1
        ]);
        $admin->markEmailAsVerified();

        $user1 = User::create([
            'name' => 'ユーザー1',
            'email' => 'user1@example.com',
            'password' => Hash::make('password'),
        ]);
        $user1->markEmailAsVerified();

        $attendance1 = Attendance::create([
            'user_id' => $user1->id,
            'date' => '2026-04-27',
            'clock_in' => '10:00',
            'clock_out' => '19:00',
        ]);

        StampCorrectionRequest::create([
            'user_id' => $user1->id,
            'attendance_id' => $attendance1->id,
            'clock_in' => '10:00',
            'clock_out' => '19:00',
            'note' => 'あ',
            'status' => 'pending',
        ]);

        $user2 = User::create([
            'name' => 'ユーザー2',
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
        ]);
        $user2->markEmailAsVerified();

        $attendance2 = Attendance::create([
            'user_id' => $user2->id,
            'date' => '2026-04-27',
            'clock_in' => '11:00',
            'clock_out' => '20:00',
        ]);

        StampCorrectionRequest::create([
            'user_id' => $user2->id,
            'attendance_id' => $attendance2->id,
            'clock_in' => '10:00',
            'clock_out' => '19:00',
            'note' => 'あ',
            'status' => 'pending',
        ]);

        $this->actingAs($admin);
        $response = $this->get('/stamp_correction_request/list?page=pending');
        $response->assertStatus(200);
        $response->assertSee('ユーザー1');
        $response->assertSee('ユーザー2');
    }

    public function test_承認済みの修正申請が全て表示されている()
    {
        $admin = User::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => 1
        ]);
        $admin->markEmailAsVerified();

        $user1 = User::create([
            'name' => 'ユーザー1',
            'email' => 'user1@example.com',
            'password' => Hash::make('password'),
        ]);
        $user1->markEmailAsVerified();

        $attendance1 = Attendance::create([
            'user_id' => $user1->id,
            'date' => '2026-04-27',
            'clock_in' => '10:00',
            'clock_out' => '19:00',
        ]);

        StampCorrectionRequest::create([
            'user_id' => $user1->id,
            'attendance_id' => $attendance1->id,
            'clock_in' => '10:00',
            'clock_out' => '19:00',
            'note' => 'あ',
            'status' => 'approved',
            'appproved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        $user2 = User::create([
            'name' => 'ユーザー2',
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
        ]);
        $user2->markEmailAsVerified();

        $attendance2 = Attendance::create([
            'user_id' => $user2->id,
            'date' => '2026-04-27',
            'clock_in' => '11:00',
            'clock_out' => '20:00',
        ]);

        StampCorrectionRequest::create([
            'user_id' => $user2->id,
            'attendance_id' => $attendance2->id,
            'clock_in' => '10:00',
            'clock_out' => '19:00',
            'note' => 'あ',
            'status' => 'approved',
            'appproved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        $this->actingAs($admin);
        $response = $this->get('/stamp_correction_request/list?page=approved');
        $response->assertStatus(200);
        $response->assertSee('ユーザー1');
        $response->assertSee('ユーザー2');
    }

    public function test_修正申請の詳細内容が正しく表示されている()
    {
        $admin = User::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => 1
        ]);
        $admin->markEmailAsVerified();

        $user = User::create([
            'name' => 'ユーザー',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);
        $user->markEmailAsVerified();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-04-27',
            'clock_in' => '10:00',
            'clock_out' => '19:00',
        ]);

        $request = StampCorrectionRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'clock_in' => '10:00',
            'clock_out' => '19:00',
            'note' => 'あ',
            'status' => 'approved',
            'appproved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        $this->actingAs($admin);
        $response = $this->get("/stamp_correction_request/approve/{$request->id}");
        $response->assertStatus(200);
        $response->assertSee('ユーザー');
        $response->assertSee('10:00');
        $response->assertSee('19:00');
        $response->assertSee('あ');

    }

    public function test_修正申請の承認処理が正しく行われる()
    {
        $admin = User::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => 1
        ]);
        $admin->markEmailAsVerified();

        $user = User::create([
            'name' => 'ユーザー',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);
        $user->markEmailAsVerified();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-04-27',
            'clock_in' => '10:00',
            'clock_out' => '19:00',
        ]);

        $request = StampCorrectionRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'clock_in' => '10:00',
            'clock_out' => '19:00',
            'note' => 'あ',
            'status' => 'pending',
        ]);

        $this->actingAs($admin);
        $this->post("/stamp_correction_request/approve/{$request->id}");

        $response = $this->get("/stamp_correction_request/approve/{$request->id}");
        $response->assertSee('承認済み');

        $this->assertDatabaseHas('stamp_correction_requests', [
            'id' => $request->id,
            'status' => 'approved',
            'approved_by' => $admin->id,
        ]);
    }
}
