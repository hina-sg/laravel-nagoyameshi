<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Admin;

class RestaurantTest extends TestCase
{
    use RefreshDatabase; 


    public function test_guest_can_access_restaurant_index(): void
{
    $response = $this->get(route("restaurants.index"));

    $response->assertStatus(200);
}

public function test_web_user_can_access_restaurant_index(): void
{
    $user = User::factory()->create();
    $response = $this->actingAs($user, "web")->get(route("restaurants.index"));

    $response->assertStatus(200);
}

public function test_admin_can_not_access_restaurant_index(): void
{
    $admin = new Admin();
    $admin->email = "admin@example.com";
    $admin->password = Hash::make("nagoyameshi");
    $admin->save();

    $response = $this->actingAs($admin, "admin")->get(route("restaurants.index"));

    $response->assertRedirect(route("admin.home"));
}
}
