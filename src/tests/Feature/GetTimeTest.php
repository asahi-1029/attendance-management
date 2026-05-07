<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class GetTimeTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_現在の日時情報がUIと同じ形式で出力されている()
    {
        Carbon::setTestNow(Carbon::create(2026, 4, 18, 14, 30));
        $user = User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->markEmailAsVerified();
        
        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertSee('2026年4月18日');
        $response->assertSee('14:30');
        $response->assertSee('土');
    }
}
