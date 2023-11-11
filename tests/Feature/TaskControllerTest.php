<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use DatabaseTransactions, WithFaker;
    /**
     * A basic feature test example.
     */
    public function test_index(): void
    {
        $user = User::factory()->create();
        $tasks = Task::factory(5)->create();

        $response = $this->actingAs($user)->json(
            'GET',
            route('tasks.index', ['text' => $tasks->first()->name])
        );

        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('data.0')->etc()
        );
    }

    public function test_show(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $response = $this->actingAs($user)->json(
            'GET',
            route('tasks.show', ['task' => $task->id])
        );

        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
                ->where('data.name', $task->name)
                ->where('data.description', $task->description)
                ->etc()
        );
    }

    public function test_store(): void
    {
        $user = User::factory()->create();

        $storeData = [
            'name' => $this->faker->name,
            'description' => $this->faker->text
        ];

        $response = $this->actingAs($user)->json(
            'POST',
            route('tasks.store'),
            $storeData
        );

        $response->assertStatus(201);
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('data')
                ->where('data.name', $storeData['name'])
                ->where('data.description', $storeData['description'])
                ->etc()
        );
    }

    public function test_update(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();
        $updateData = [
            'name' => $this->faker->name,
            'description' => $this->faker->text
        ];

        $response = $this->actingAs($user)->json(
            'PUT',
            route('tasks.update', $task),
            $updateData
        );

        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) =>
        $json->has('data')
            ->where('data.name', $updateData['name'])
            ->where('data.description', $updateData['description'])
            ->etc()
        );
    }

    public function test_complete(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['completed_at' => null]);

        $response = $this->actingAs($user)->json(
            'PUT',
            route('tasks.complete', $task)
        );

        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) =>
        $json->has('data')
            ->whereNot('data.completed_at', null)
            ->etc()
        );
    }

    public function test_destroy(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $response = $this->actingAs($user)->json(
            'DELETE',
            route('tasks.destroy', $task)
        );

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }
}
