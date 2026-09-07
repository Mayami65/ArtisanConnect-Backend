<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ServiceJob;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Controller',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_verified' => true,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_users_list_and_verify_artisan(): void
    {
        $artisan = User::create([
            'name' => 'Kwame Carpenter',
            'email' => 'kwame@artisan.com',
            'password' => Hash::make('password'),
            'role' => 'artisan',
            'category' => 'Carpentry',
            'is_verified' => false,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('Kwame Carpenter');
        $response->assertSee('Pending Verification');

        // Approve verification
        $verifyResponse = $this->actingAs($this->admin)->post("/admin/users/{$artisan->id}/verify", [
            'is_verified' => 1,
            'verification_notes' => 'Trade certificate checked and approved.',
        ]);

        $verifyResponse->assertRedirect();
        $this->assertTrue($artisan->fresh()->is_verified);
        $this->assertEquals('Trade certificate checked and approved.', $artisan->fresh()->verification_notes);
    }

    public function test_admin_can_toggle_user_active_status(): void
    {
        $client = User::create([
            'name' => 'Bad Actor',
            'email' => 'bad@client.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/users/{$client->id}/toggle-active");
        $response->assertRedirect();
        $this->assertFalse($client->fresh()->is_active);

        // Activate again
        $this->actingAs($this->admin)->post("/admin/users/{$client->id}/toggle-active");
        $this->assertTrue($client->fresh()->is_active);
    }

    public function test_admin_can_update_service_job_status(): void
    {
        $client = User::create([
            'name' => 'Job Poster',
            'email' => 'poster@client.com',
            'password' => Hash::make('password'),
            'role' => 'client',
        ]);

        $job = ServiceJob::create([
            'client_id' => $client->id,
            'title' => 'Fix leaking roof in Osu',
            'description' => 'Heavy rainfall causes leakage in the main room.',
            'category' => 'Roofing',
            'budget' => 450.00,
            'location' => 'Osu, Accra',
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/jobs/{$job->id}");
        $response->assertStatus(200);
        $response->assertSee('Fix leaking roof in Osu');

        // Update status to in_progress
        $patchResponse = $this->actingAs($this->admin)->patch("/admin/jobs/{$job->id}/status", [
            'status' => 'in_progress',
        ]);

        $patchResponse->assertRedirect();
        $this->assertEquals('in_progress', $job->fresh()->status);
    }

    public function test_admin_can_create_and_delete_categories(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/categories', [
            'name' => 'Welding & Fabrication',
            'icon_name' => 'engineering',
            'color_hex' => '#546E7A',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'name' => 'Welding & Fabrication',
        ]);

        $category = Category::query()->where('name', 'Welding & Fabrication')->first();
        $deleteResponse = $this->actingAs($this->admin)->delete("/admin/categories/{$category->id}");
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('categories', [
            'name' => 'Welding & Fabrication',
        ]);
    }
}
