<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\ToDoList;

class ToDoListTest extends TestCase
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
        $response = $this->actingAs($user)->getJson('/api/1.0/lists');
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_index_filters(){
        $this->seed();
        $user = User::first();
        $response = $this->actingAs($user)->getJson('/api/1.0/lists?name=a');
        //$response->dump();
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_create(){
        $this->seed();
        $user = User::first();
        
        $response = $this->actingAs($user)->postJson('/api/1.0/lists', ['name' => "test",'date' => '2022/12/12']);

        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_create_collide_dates(){
        $this->seed();
        $user = User::first();
        
        $response = $this->actingAs($user)->postJson('/api/1.0/lists', ['name' => "test",'date' => '2022/12/12']);

        $response = $this->actingAs($user)->postJson('/api/1.0/lists', ['name' => "test",'date' => '2022/12/12']);

        $response
            ->assertStatus(400)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_update_other_user_list(){
        $this->seed();
        
        $list = ToDoList::first();
        $user = User::where('id','!=',$list->user_id)->first();

        $response = $this->actingAs($user)->putJson('/api/1.0/lists/'.$list->id, ['name' => "test",'date' => '2022/12/12']);

        $response
            ->assertStatus(400)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_update(){
        $this->seed();
        
        $list = ToDoList::first();
        $user = User::where('id',$list->user_id)->first();

        $response = $this->actingAs($user)->putJson('/api/1.0/lists/'.$list->id, ['name' => "test",'date' => '2022/12/12']);
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
}
