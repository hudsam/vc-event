<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('Maxy Event');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        Http::fake([
            '*/api/events*' => Http::response([
                'status' => 'success',
                'data' => []
            ], 200)
        ]);

        $user = User::factory()->create([
            'email' => 'hudsam@maxy.academy'
        ]);
        
        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Overview');
    }
}
