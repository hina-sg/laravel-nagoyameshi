<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurants;
use App\Models\Category;

class CategoryTest extends TestCase
{

    use RefreshDatabase;

    public function test_guest_can_not_access_admin_category_index(): void
    {
        $response = $this->get(route("admin.categories.index"));

        $response->assertRedirect("admin/login");
    }

    public function test_web_user_can_not_access_admin_category_index(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->get(route("admin.categories.index"));

        $response->assertRedirect("admin/login");
    }

    public function test_admin_can_access_admin_category_index(): void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $response = $this->actingAs($admin, "admin")->get(route("admin.categories.index"));
        $response->assertOK();
    }

    public function test_guest_can_not_store_admin_category(): void
    {
        $new_category = new Category();
        $new_category->name = "テスト";
        $new_category->save();

        $response = $this->get(route("admin.categories.store"));
        $response->assertRedirect("admin/login");
    }

    public function test_web_user_can_not_store_admin_category(): void
    {
        $user = User::factory()->create();

        $new_category = new Category();
        $new_category->name = "テスト";
        $new_category->save();

        $response = $this->actingAs($user, "web")->get(route("admin.categories.store"));
        $response->assertRedirect("admin/login");
    }

    public function test_admin_can_store_admin_category(): void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $new_category = new Category();
        $new_category->name = "テスト";
        $new_category->save();

        $response = $this->actingAs($admin, "admin")->get(route("admin.categories.store"));
        
        $this->assertDatabaseHas("categories", ["name" => "テスト"]);
        $response->assertOK();
    }

    public function test_guest_can_not_update_admin_category(): void
    {
        $response = $this->get(route("admin.categories.update", ["category" => "1"]));
        $response->assertRedirect("admin/login");
    }

    public function test_web_user_can_not_update_admin_category(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, "web")->get(route("admin.categories.update", ["category" => "1"]));
        $response->assertRedirect("admin/login");
    }

    public function test_admin_can_update_admin_category(): void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $new_category = new Category();
        $new_category->name = "テスト";
        $new_category->save();

        $update_category = ["name" => "テスト2"];

        $response = $this->actingAs($admin, "admin")->patch(route("admin.categories.update", ["category" => "$new_category->id"]), $update_category);
        
        $this->assertDatabaseHas("categories", ["name" => "テスト2"]);
        $response->assertRedirect("admin/categories");
    }

    public function test_guest_can_not_destroy_admin_category(): void
    {
        $response = $this->get(route("admin.categories.destroy", ["category" => "1"]));
        $response->assertRedirect("admin/login");
    }

    public function test_web_user_can_not_destroy_admin_category(): void
    {
        $user = User::factory()->create();

        $new_category = new Category();
        $new_category->name = "テスト";
        $new_category->save();

        $response = $this->get(route("admin.categories.destroy", ["category" => "$new_category->id"]));
        $response->assertRedirect("admin/login");
    }

    public function test_admin_can_destroy_admin_category(): void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $category = new Category();
        $category->name = "テスト";
        $category->save();

        $response = $this->actingAs($admin, "admin")->delete(route("admin.categories.destroy", ["category" => "$category->id"]));
        
        $this->assertDatabaseMissing("categories", ["name" => "テスト"]);
        $response->assertRedirect("admin/categories");
    }
}