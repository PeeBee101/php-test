<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\Task;

class TaskApiTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'mongodb']);
        config(['database.connections.mongodb.database' => 'laravel_test_db']);

        \App\Models\Task::truncate();
    }

    public function test_create_task()
    {
        $response = $this->postJson('/api/tasks', [
            'name' => 'Test Task',
            'description' => 'This is a valid description with more than 10 characters.',
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Test Task']);
    }

    public function test_list_tasks()
    {
        Task::create([
            'name' => 'Task 1',
            'description' => 'Description for Task 1',
            'secure_id' => \Illuminate\Support\Str::uuid(),
        ]);

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
                 ->assertJsonCount(1);
    }

    public function test_update_task()
    {
        $task = Task::create([
            'name' => 'Initial Task',
            'description' => 'Initial description.',
            'secure_id' => (string) Str::uuid(), // explicitly set secure_id
        ]);

        $response = $this->putJson('/api/tasks/' . $task->secure_id, [
            'name' => 'Updated Task',
            'description' => 'Updated valid description.',
        ]);

        $response->assertStatus(200)
                ->assertJsonFragment(['name' => 'Updated Task']);
    }

    public function test_delete_task()
    {
        $task = Task::create([
            'name' => 'Task to delete',
            'description' => 'This task will be deleted.',
            'secure_id' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        $response = $this->deleteJson('/api/tasks/' . $task->secure_id);

        $response->assertStatus(200)
                ->assertJsonFragment(['message' => 'Task deleted']);
    }

    public function test_restore_task()
    {
        $task = Task::create([
            'name' => 'Task to restore',
            'description' => 'This task will be restored.',
            'secure_id' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        $task->delete(); // soft delete

        $response = $this->postJson('/api/tasks/' . $task->secure_id . '/restore');

        $response->assertStatus(200)
                ->assertJsonFragment(['message' => 'Task restored']);
    }

    public function test_validation_on_create()
    {
        $response = $this->postJson('/api/tasks', [
            'name' => 'A',
            'description' => 'Too short',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'description']);
    }
    protected function tearDown(): void
    {
        Task::truncate(); // clean up after each test
        parent::tearDown();
    }
}