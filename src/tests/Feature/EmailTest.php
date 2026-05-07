<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class EmailTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_会員登録後に認証メールが送信される()
    {
        Notification::fake();//送信したフリだけ

        $data = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $this->post('/register', $data);

        $user = User::where('email','test@example.com')->first();

        //Notification::assertSentTo(送信先, 通知クラス);
        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );
    }

    public function test_認証はこちらからボタンを押すとメール認証サイトを遷移する()
    {
        $user = User::create([
            'name' => 'ユーザー',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => null
        ]);
        $this->actingAs($user);
        $response = $this->post('/email/verification-notification');
        $response->assertStatus(302);
    }
    
    public function test_メール認証完了後勤怠一覧画面に遷移する()
    {
        $user = User::create([
            'name' => 'ユーザー',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        // 認証URLを作る
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email)
            ]
        );

        // 認証URLへアクセス
        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect('/attendance');
    }
    
}
