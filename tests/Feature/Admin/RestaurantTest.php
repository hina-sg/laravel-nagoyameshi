<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;

class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_guest_can_not_access_admin_restaurants_index(): void
    {
        $response = $this->get(route("admin.restaurants.index"));

        $response->assertRedirect("admin/login");
    }

    public function test_web_user_can_not_access_admin_restaurant_index(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->get(route("admin.restaurants.index"));

        $response->assertRedirect("admin/login");
    }

    public function test_admin_can_access_admin_restaurants_index(): void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $response = $this->actingAs($admin, "admin")->get(route("admin.restaurants.index"));

        $response->assertOK();
    }

    public function test_guest_can_not_access_admin_restaurants_show(): void
    {
        $response = $this->get(route("admin.restaurants.show", ["restaurant" => "3"]));

        $response->assertRedirect("admin/login");
    }

    public function test_web_user_can_not_access_admin_restaurants_show(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->get(route("admin.restaurants.show", ["restaurant" => "3"]));

        $response->assertRedirect("admin/login");
    }

    public function test_admin_can_access_admin_restaurants_show(): void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $response = $this->actingAs($admin, "admin")->get("/admin/restaurants", ["restaurant" => "3"]);

        $response->assertOK();
    }

    public function test_guest_can_not_access_admin_restaurants_create(): void
    {
        $response = $this->get(route("admin.restaurants.create"));

        $response->assertRedirect("admin/login");
    }

    public function test_web_user_can_not_access_admin_restaurants_create(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->get(route("admin.restaurants.create"));

        $response->assertRedirect("admin/login");
    }

    public function test_admin_can_access_admin_restaurants_create(): void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $response = $this->actingAs($admin, "admin")->get("admin/restaurants/create");

        $response->assertOK();
    }

    public function test_guest_can_not_store_admin_restaurant(): void
    {
        $response = $this->get(route("admin.restaurants.store"));

        $response->assertRedirect("admin/login");
    }

    public function test_web_user_can_not_store_admin_restaurant(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->get(route("admin.restaurants.store"));

        $response->assertRedirect("admin/login");
    }

    public function test_admin_can_store_admin_restaurant(): void
    {
        $new_restaurant = Restaurant::factory()->make()->toArray();

        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $response = $this->actingAs($admin, "admin")->post(route("admin.restaurants.store", $new_restaurant));
        $response->assertRedirect(route("admin.restaurants.index"));
        $this->assertDatabaseHas("restaurants", ["name" => "テスト"]);
    }

    public function test_guest_can_not_access_admin_restaurant_edit(): void
    {
        $response = $this->get(route("admin.restaurants.edit", ["restaurant" => "3"]));

        $response->assertRedirect("admin/login");
    }

    public function test_web_user_can_not_access_admin_restaurant_edit(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->get(route("admin.restaurants.edit", ["restaurant" => "3"]));

        $response->assertRedirect("admin/login");
    }

    public function test_admin_can_access_admin_restaurant_edit(): void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $response = $this->actingAs($admin, "admin")->get("/admin/restaurants", ["restaurant" => "3"], "/edit");

        $response->assertOK();
    }

    public function test_guest_can_not_update_admin_restaurant(): void
    {
        $response = $this->get(route("admin.restaurants.update", ["restaurant" => "3"]));

        $response->assertRedirect("admin/login");
    }

    public function test_web_user_can_not_update_admin_restaurant(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->get(route("admin.restaurants.update", ["restaurant" => "3"]));

        $response->assertRedirect("admin/login");
    }

    public function test_admin_can_update_admin_restaurant(): void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();
        
        $restaurant = Restaurant::factory()->create();
        $new_restaurant = [
            "name" => "アップデートテスト",
            "description" => "新しい説明",
            "lowest_price" => 1000,
            "highest_price" => 2000,
            "postal_code" => "1234567",
            "address" => "新しい住所",
            "opening_time" => "10:00",
            "closing_time" => "20:00",
            "seating_capacity" => 50,
        ];

        $response = $this->actingAs($admin, "admin")->patch(route("admin.restaurants.update", ["restaurant" => $restaurant->id]), $new_restaurant);
        $this->assertDatabaseHas("restaurants", ["name" => "アップデートテスト"]);
    }

    public function test_guest_can_not_destroy_admin_restaurant(): void
    {
        $response = $this->get(route("admin.restaurants.destroy", ["restaurant" => "3"]));

        $response->assertRedirect("admin/login");
    }

    public function test_web_user_can_not_destroy_admin_restaurant(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->get(route("admin.restaurants.destroy", ["restaurant" => "3"]));

        $response->assertRedirect("admin/login");
    }

    public function test_admin_can_destroy_admin_restaurant(): void
    {
        $new_restaurant = Restaurant::factory()->create();

        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

    
        $response = $this->actingAs($admin, "admin")->delete(route("admin.restaurants.destroy", ["restaurant" => $new_restaurant->id]));
        $this->assertDatabaseMissing("restaurants", ["name" => "テスト"]);
    }
}
