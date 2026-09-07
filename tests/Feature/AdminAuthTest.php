<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
        $response->assertSee('Admin Login');
    }

    public function test_unauthenticated_user_visiting_admin_is_redirected_to_login(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_user_can_authenticate_and_access_dashboard(): void
    {
        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@artisanconnect.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
            'is_verified' => true,
            'is_active' => true,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@artisanconnect.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);

        $dashboardResponse = $this->actingAs($admin)->get('/admin/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Executive Dashboard');
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $client = User::create([
            'name' => 'John Client',
            'email' => 'client@artisanconnect.com',
            'password' => Hash::make('secret123'),
            'role' => 'client',
            'is_verified' => true,
            'is_active' => true,
        ]);

        // Trying to log in via admin login form
        $response = $this->post('/admin/login', [
            'email' => 'client@artisanconnect.com',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        // Trying to access dashboard while authenticated as client
        $directResponse = $this->actingAs($client)->get('/admin/dashboard');
        $directResponse->assertRedirect('/admin/login');
    }

    public function test_admin_can_logout(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@artisanconnect.com',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/logout');
        $response->assertRedirect('/admin/login');
        $this->assertGuest();
    }
}
