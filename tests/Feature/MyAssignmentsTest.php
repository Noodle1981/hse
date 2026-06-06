<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyAssignmentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_load_my_assignments_page()
    {
        // 1. Create a user (tecnico)
        $user = User::factory()->create();

        // 2. Act as user
        $response = $this->actingAs($user)->get(route('tecnico.checklists.tasks.index'));

        // 3. Assert status is 200 (Inertia view)
        $response->assertStatus(200);
    }
}
