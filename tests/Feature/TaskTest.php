<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\ToDoList;

class TaskTest extends TestCase
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

        $list = ToDoList::first();
        $user = User::where('id',$list->user_id)->first();

        $response = $this->actingAs($user)->getJson('/api/1.0/lists/'.$list->id.'/tasks');
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_index_with_filter(){
        $this->seed();

        $list = ToDoList::first();
        $user = User::where('id',$list->user_id)->first();

        $response = $this->actingAs($user)->getJson('/api/1.0/lists/'.$list->id.'/tasks?name=a');
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_create(){
        User::factory(10)->create();
        ToDoList::factory(10)->create();

        $list = ToDoList::first();
        $user = User::where('id',$list->user_id)->first();
        $task = Task::factory()->make();

        $response = $this->actingAs($user)->postJson('/api/1.0/lists/'.$list->id.'/tasks', ['name' => $task->name,'start' => $task->start,'end' => $task->end]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_create_overlapping(){
        User::factory(10)->create();
        ToDoList::factory(10)->create();

        $list = ToDoList::first();
        $user = User::where('id',$list->user_id)->first();

        $this->actingAs($user)->postJson('/api/1.0/lists/'.$list->id.'/tasks', ['name' => 'test1','start' => '12:00:00','end' => '13:00:00']);

        $response = $this->actingAs($user)->postJson('/api/1.0/lists/'.$list->id.'/tasks', ['name' => 'test2','start' => '12:30:00','end' => '13:10:00']);

        $response
            ->assertStatus(400)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_update(){
        $user = User::factory()->create();
        $list = ToDoList::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['to_do_list_id' => $list->id,'start' => '11:00:00','end' => '11:50:00']);

        $response = $this->actingAs($user)->putJson('/api/1.0/tasks/'.$task->id, ['name' => 'test1','start' => '12:00:00','end' => '13:00:00']);
        $response
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_update_other_user(){
        User::factory(10)->create();
        ToDoList::factory(10)->create();

        $list = ToDoList::first();
        $user = User::where('id','!=',$list->user_id)->first();

        $task = Task::factory()->create(['to_do_list_id' => $list->id,'start' => '11:00:00','end' => '11:50:00']);

        $response = $this->actingAs($user)->putJson('/api/1.0/tasks/'.$task->id, ['name' => 'test1','start' => '12:00:00','end' => '13:00:00']);
        $response
            ->assertStatus(400)
            ->assertJson([
                'success' => false,
            ]);

    }

}
