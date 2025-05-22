<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Admin;
use App\Models\Term;

class TermTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_not_access_term_index(): void
    {
        $response = $this->get(route("admin.terms.index"));

        $response->assertRedirect("admin/login");
    }

    public function test_web_user_can_not_access_term_index(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->get(route("admin.terms.index"));

        $response->assertRedirect("admin/login");
    }

    public function test_admin_can_access_term_index(): void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $term = Term::factory()->create();

        $response = $this->actingAs($admin, "admin")->get(route("admin.terms.index"));

        $response->assertOK();
    }

    public function test_guest_can_not_access_term_edit(): void
    {
        $response = $this->get(route("admin.terms.edit", ["term" => "1"]));

        $response->assertRedirect("admin/login");
    }

    public function test_web_user_can_not_access_term_edit(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->get(route("admin.terms.edit", ["term" => "1"]));

        $response->assertRedirect("admin/login");
    }

    public function test_admin_can_access_term_edit(): void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $term = Term::factory()->create();

        $response = $this->actingAs($admin, "admin")->get("/admin/terms", ["terms" => "1"], "/edit");

        $response->assertOK();
    }

    public function test_guest_can_not_update_term(): void
    {
        $response = $this->get(route("admin.terms.update", ["term" => "1"]));

        $response->assertRedirect("admin/login");
    }

    public function test_web_user_can_not_update_term(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->get(route("admin.terms.update", ["term" => "1"]));

        $response->assertRedirect("admin/login");
    }

    public function test_admin_can_update_term(): void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $term = Term::factory()->create(); 
        $update_term = [
            "content" => "テスト更新",
        ];
        
        $response = $this->actingAs($admin, "admin")->patch(route("admin.terms.update", ["term" => $term->id]),$update_term);
        $this->assertDatabaseHas("terms", $update_term);
        $response->assertRedirect(route('admin.terms.index'));
    }

}
