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

class UpdateAttendanceDetailListTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 27, 10, 0));

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
            'clock_in' => Carbon::create(2026, 4, 27, 10, 0),
            'clock_out' => Carbon::create(2026, 4, 27, 19, 0),
        ]);

        $response = $this->get("/attendance/detail/{$attendance->id}");
        $response->assertStatus(200);

        $response = $this->post("/attendance/detail/{$attendance->id}", [
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
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->markEmailAsVerified();
        $this->actingAs($user);

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

        $response = $this->get("/attendance/detail/{$attendance->id}");
        $response->assertStatus(200);

        $response = $this->post("/attendance/detail/{$attendance->id}", [
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
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->markEmailAsVerified();
        $this->actingAs($user);

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

        $response = $this->get("/attendance/detail/{$attendance->id}");
        $response->assertStatus(200);

        $response = $this->post("/attendance/detail/{$attendance->id}", [
            'clock_in' => Carbon::create(2026, 4, 27, 10, 0)->format('H:i'),
            'clock_out' => Carbon::create(2026, 4, 27, 19, 0)->format('H:i'),
            'note' => 'テスト',
            'breaks' => [
                [
                    'start_time' => '12:00',
                    'end_time' => '20:00',
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
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->markEmailAsVerified();
        $this->actingAs($user);

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

        $response = $this->get("/attendance/detail/{$attendance->id}");
        $response->assertStatus(200);

        $response = $this->post("/attendance/detail/{$attendance->id}", [
            'clock_in' => Carbon::create(2026, 4, 27, 10, 0)->format('H:i'),
            'clock_out' => Carbon::create(2026, 4, 27, 19, 0)->format('H:i'),
            'note' => '',
            'breaks' => [
                [
                    'start_time' => '12:00',
                    'end_time' => '13:00',
                ]
            ]
        ]);

        $response->assertSessionHasErrors([
            'note' => '備考を記入してください'
        ]);

    }

    public function test_修正申請処理が実行される()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 27, 10, 0));

        $user = User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->markEmailAsVerified();
        $this->actingAs($user);

        $adminUser = User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => 1
        ]);
        
        $adminUser->markEmailAsVerified();

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

        $response = $this->get("/attendance/detail/{$attendance->id}");
        $response->assertStatus(200);

        $response = $this->post("/attendance/detail/{$attendance->id}", [
            'clock_in' => Carbon::create(2026, 4, 27, 10, 0)->format('H:i'),
            'clock_out' => Carbon::create(2026, 4, 27, 19, 0)->format('H:i'),
            'note' => 'テスト',
            'breaks' => [
                [
                    'start_time' => '12:00',
                    'end_time' => '13:00',
                ]
            ]
        ]);

        $this->assertDatabaseHas('stamp_correction_requests', [
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'note' => 'テスト',
        ]);

        $this->actingAs($adminUser);
        $response = $this->get('/stamp_correction_request/list?page=pending');
        $response->assertSee('テスト');

        $request = \App\Models\StampCorrectionRequest::first();
        $response = $this->get("/stamp_correction_request/approve/{$request->id}");
        $response->assertSee($user->name);
        $response->assertSee('テスト');

    }
    
    public function test_「承認待ち」にログインユーザーが行った申請が全て表示されていること()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 27, 10, 0));

        $user = User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->markEmailAsVerified();
        $this->actingAs($user);

        $attendance1 = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => Carbon::create(2026, 4, 27, 10, 0),
            'clock_out' => Carbon::create(2026, 4, 27, 19, 0),
        ]);

        BreakTime::create([
            'attendance_id' => $attendance1->id,
            'start_time' => Carbon::create(2026, 4, 27, 12, 0),
            'end_time' => Carbon::create(2026, 4, 27, 13, 0),
        ]);

        Carbon::setTestNow(Carbon::create(2026, 4, 28, 10, 0));

        $attendance2 = Attendance::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => Carbon::create(2026, 4, 28, 10, 0),
            'clock_out' => Carbon::create(2026, 4, 28, 19, 0),
        ]);

        BreakTime::create([
            'attendance_id' => $attendance2->id,
            'start_time' => Carbon::create(2026, 4, 28, 12, 0),
            'end_time' => Carbon::create(2026, 4, 28, 13, 0),
        ]);

        $response = $this->get("/attendance/detail/{$attendance1->id}");
        $response->assertStatus(200);

        $response = $this->post("/attendance/detail/{$attendance1->id}", [
            'clock_in' => Carbon::create(2026, 4, 27, 10, 0)->format('H:i'),
            'clock_out' => Carbon::create(2026, 4, 27, 19, 0)->format('H:i'),
            'note' => 'テスト1',
            'breaks' => [
                [
                    'start_time' => '13:00',
                    'end_time' => '14:00',
                ]
            ]
        ]);

        $response = $this->get("/attendance/detail/{$attendance2->id}");
        $response->assertStatus(200);

        $response = $this->post("/attendance/detail/{$attendance2->id}", [
            'clock_in' => Carbon::create(2026, 4, 28, 10, 0)->format('H:i'),
            'clock_out' => Carbon::create(2026, 4, 28, 19, 0)->format('H:i'),
            'note' => 'テスト2',
            'breaks' => [
                [
                    'start_time' => '13:00',
                    'end_time' => '14:00',
                ]
            ]
        ]);

        $response = $this->get('/stamp_correction_request/list?page=pending');
        $response->assertSee('テスト1');
        $response->assertSee('テスト2');
    }

    public function test_「承認済み」に管理者が承認した修正申請が全て表示されている()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 27, 10, 0));

        $user = User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->markEmailAsVerified();
        $this->actingAs($user);

        $adminUser = User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => 1
        ]);
        
        $adminUser->markEmailAsVerified();

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

        $response = $this->get("/attendance/detail/{$attendance->id}");
        $response->assertStatus(200);

        $response = $this->post("/attendance/detail/{$attendance->id}", [
            'clock_in' => Carbon::create(2026, 4, 27, 10, 0)->format('H:i'),
            'clock_out' => Carbon::create(2026, 4, 27, 19, 0)->format('H:i'),
            'note' => 'テスト',
            'breaks' => [
                [
                    'start_time' => '13:00',
                    'end_time' => '14:00',
                ]
            ]
        ]);

        $this->actingAs($adminUser);
        $request = \App\Models\StampCorrectionRequest::first();
        $response = $this->post("/stamp_correction_request/approve/{$request->id}");

        $response = $this->get('/stamp_correction_request/list?page=approved');
        $response->assertSee('テスト');
    
    }

    public function test_各申請の「詳細」を押下すると勤怠詳細画面に遷移する()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 27, 10, 0));

        $user = User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->markEmailAsVerified();
        $this->actingAs($user);

        $adminUser = User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => 1
        ]);
        
        $adminUser->markEmailAsVerified();

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

        $response = $this->get("/attendance/detail/{$attendance->id}");
        $response->assertStatus(200);

        $response = $this->post("/attendance/detail/{$attendance->id}", [
            'clock_in' => Carbon::create(2026, 4, 27, 10, 0)->format('H:i'),
            'clock_out' => Carbon::create(2026, 4, 27, 19, 0)->format('H:i'),
            'note' => 'テスト',
            'breaks' => [
                [
                    'start_time' => '13:00',
                    'end_time' => '14:00',
                ]
            ]
        ]);

        $request = \App\Models\StampCorrectionRequest::first();

        // 一覧画面
        $this->actingAs($adminUser);
        $response = $this->get('/stamp_correction_request/list?page=pending');

        // ① リンクが存在するか
        $response->assertSee("/stamp_correction_request/approve/{$request->id}");

        // ② クリック（GETで代用）
        $response = $this->get("/stamp_correction_request/approve/{$request->id}");

        // ③ 遷移先確認
        $response->assertStatus(200);
        $response->assertSee($user->name); // 誰の申請か
        $response->assertSee('テスト');   // 備考
    }
}
