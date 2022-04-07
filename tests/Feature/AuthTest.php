<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_connection()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_registration(){
        $user = User::factory()->make();
        $response = $this->postJson('/api/1.0/register', ['name' => $user->name,'email' => $user->email,'password' => 'password','c_password' => 'password']);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_login(){
        $this->seed();
        $user = User::first();

        $response = $this->postJson('/api/1.0/login', ['email' => $user->email,'password' => 'password']);
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
}
