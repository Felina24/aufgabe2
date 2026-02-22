<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/attendance');
        $this->assertAuthenticatedAs($user);
    }


    /** @test */
    public function admin_can_access_staff_attendance()
    {
        $this->withoutMiddleware();

        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->get('/admin/staff/list');

        $response->assertStatus(200);
    }


    /** @test */
    public function csv_can_be_downloaded()
    {
        $this->withoutMiddleware();

        $admin = Admin::factory()->create();
        $user  = User::factory()->create();

        $response = $this->actingAs($admin, 'admin')
            ->get("/admin/attendance/staff/{$user->id}/csv?month=2026-02");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
