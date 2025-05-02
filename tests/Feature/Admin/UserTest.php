<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Admin;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_not_access_users_index() :void
    {
        $response = $this->get(route("admin.users.index"));

        $response->assertRedirect("admin/login");
    }

    public function test_user_can_not_access_users_index() :void
    {
    
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->get(route("admin.users.index"));

        $response->assertRedirect("admin/login");
    }

    public function test_admin_can_access_users_index() :void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $response = $this->actingAs($admin, "admin")->get("/admin/users/index");

        $response->assertOK();
    }

    public function test_guest_can_not_access_users_show() :void
    {
        $response = $this->get(route("admin.users.show"));

        $response->assertRedirect("admin/login");
    }

    public function test_user_can_not_access_users_show() :void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->get(route("admin.users.show"));

        $response->assertRedirect("admin/login");
    }

    public function test_admin_can_access_users_show() :void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $response = $this->actingAs($admin, "admin")->get("/admin/users/show");

        $response->assertOK();
    }
}
