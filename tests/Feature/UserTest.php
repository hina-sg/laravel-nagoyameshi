<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Admin;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_not_access_user_index(): void 
    {
        $response = $this->get(route("user.index"));

        $response->assertRedirect(route("login"));
    }

    public function test_can_access_user_index(): void 
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->get(route("user.index"));

        $response->assertOk();
    }

    public function test_admin_can_not_access_user_index(): void 
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $response = $this->actingAs($admin, "admin")->get(route("user.index"));

        $response->assertRedirect(route('login'));
    }


        public function test_guest_can_not_access_user_edit(): void 
    {
        $user = User::factory()->create();
        $response = $this->get(route("user.edit", ["user" => $user->id]));

        $response->assertRedirect(route("login"));
    }

    public function test_web_user_can_access_own_user_edit(): void 
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->get(route("user.edit", ["user" => $user->id]));

        $response->assertOk();
    }

    public function test_web_user_can_not_access_other_user_edit(): void 
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user, "web")->get(route("user.edit", ["user" => $user2->id]));
        
        $response->assertStatus(302);
    }

    public function test_admin_can_not_access_user_edit(): void 
    {
        $user = User::factory()->create();
        
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $response = $this->actingAs($admin, "admin")->get(route("user.edit", ["user" => $user->id]));

        $response->assertStatus(302);
    }

            public function test_guest_can_not_access_user_update(): void 
    {
        $user = User::factory()->create();
        $response = $this->put(route("user.update", ["user" => $user->id]));

        $response->assertRedirect(route("login"));
    }

    public function test_web_user_can_access_own_user_update(): void 
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->put(route("user.update", ["user" => $user->id]));

        $response->assertRedirect(route('user.index'));
    }

    public function test_web_user_can_not_access_other_user_update(): void 
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user, "web")->put(route("user.update", ["user" => $user2->id]));
        
        $response->assertStatus(302);
    }

    public function test_admin_can_not_access_user_update(): void 
    {
        $user = User::factory()->create();

        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $response = $this->actingAs($admin, "admin")->put(route("user.update", ["user" => $user->id]));

        $response->assertStatus(302);
    }
}

