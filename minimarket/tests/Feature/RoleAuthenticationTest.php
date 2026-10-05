<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_redirected_to_admin_dashboard_after_login(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_kasir_redirected_to_kasir_dashboard_after_login(): void
    {
        $kasir = User::factory()->create([
            'role' => 'kasir',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $kasir->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('kasir.dashboard'));
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Admin');
    }

    public function test_admin_forbidden_from_kasir_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/kasir/dashboard');

        $response->assertStatus(403);
    }

    public function test_kasir_can_access_kasir_dashboard(): void
    {
        $kasir = User::factory()->create(['role' => 'kasir']);

        $response = $this->actingAs($kasir)->get('/kasir/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Kasir');
    }

    public function test_kasir_forbidden_from_admin_dashboard(): void
    {
        $kasir = User::factory()->create(['role' => 'kasir']);

        $response = $this->actingAs($kasir)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');

        $responseKasir = $this->get('/kasir/dashboard');
        $responseKasir->assertRedirect('/login');
    }

    public function test_admin_can_access_products_module(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/products');

        $response->assertStatus(200);
    }

    public function test_kasir_forbidden_from_products_module(): void
    {
        $kasir = User::factory()->create(['role' => 'kasir']);

        $response = $this->actingAs($kasir)->get('/products');

        $response->assertStatus(403);
    }

    public function test_both_admin_and_kasir_can_access_transactions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kasir = User::factory()->create(['role' => 'kasir']);

        $responseAdmin = $this->actingAs($admin)->get('/transactions');
        $responseAdmin->assertStatus(200);

        $responseKasir = $this->actingAs($kasir)->get('/transactions');
        $responseKasir->assertStatus(200);
    }
}
