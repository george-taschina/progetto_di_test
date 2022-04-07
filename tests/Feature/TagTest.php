<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Tag;
use App\Models\Task;

class TagTest extends TestCase
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

    public function test_index(){
        $this->seed();
        $user = User::first();
        $response = $this->actingAs($user)->getJson('/api/1.0/tags');
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_create(){
        $this->seed();
        $user = User::first();
        
        $response = $this->actingAs($user)->postJson('/api/1.0/tags', ['name' => "test"]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_create_with_same_name(){
        $this->seed();
        $user = User::first();
        
        $response = $this->actingAs($user)->postJson('/api/1.0/tags', ['name' => "test"]);
        $response = $this->actingAs($user)->postJson('/api/1.0/tags', ['name' => "test"]);

        $response
            ->assertStatus(400)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_update(){
        $this->seed();
        $user = User::first();
        $tag = Tag::first();

        $response = $this->actingAs($user)->putJson('/api/1.0/tags/'.$tag->id, ['name' => "test"]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_delete(){
        $this->seed();
        $user = User::first();
        $tag = Tag::first();

        $response = $this->actingAs($user)->deleteJson('/api/1.0/tags/'.$tag->id);
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_attach(){
        $this->seed();
        $user = User::first();
        $tag = Tag::first();
        $task = Task::first();

        $response = $this->actingAs($user)->postJson('/api/1.0/tasks/'.$task->id.'/tags/'.$tag->id);
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_detach(){
        $this->seed();
        $user = User::first();
        $tag = Tag::first();
        $task = Task::first();

        $response = $this->actingAs($user)->deleteJson('/api/1.0/tasks/'.$task->id.'/tags/'.$tag->id);
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

}
