<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Admin;
use App\Models\Company;

class CompanyTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_guest_can_not_access_company_index(): void
    {
        
        $response = $this->get(route("admin.company.index"));

        $response->assertRedirect("admin/login");
    }

    public function test_web_user_can_not_access_company_index(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->get("/admin/company");

        $response->assertRedirect("admin/login");
    }

    public function test_admin_can_access_company_index(): void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $company = Company::factory()->create();

        $response = $this->actingAs($admin, "admin")->get("/admin/company");

        $response->assertOK();
    }

    public function test_guest_can_not_access_company_edit(): void
    {
        $response = $this->get("/admin/company", ["company" => "1"], "/edit");

        $response->assertRedirect("admin/login");
    }

    public function test_web_user_can_not_access_company_edit(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->get("/admin/company", ["company" => "1"], "/edit");

        $response->assertRedirect("admin/login");
    }

    public function test_admin_can_access_company_edit(): void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $company = Company::factory()->create();

        $response = $this->actingAs($admin, "admin")->get("/admin/company", ["company" => "1"], "/edit");

        $response->assertOK();
    }

    public function test_guest_can_not_update_company(): void
    {
        $response = $this->get(route("admin.company.update", ["company" => "1"]));

        $response->assertRedirect("admin/login");
    }

    public function test_web_user_can_not_update_company(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->get(route("admin.company.update", ["company" => "1"]));

        $response->assertRedirect("admin/login");
    }

    public function test_admin_can_update_company(): void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();
        
        $company = Company::factory()->create();
        $update_company = [
            'name' => 'テスト更新',
            'postal_code' => '1234567',
            'address' => 'テスト更新',
            'representative' => 'テスト更新',
            'establishment_date' => 'テスト更新',
            'capital' => 'テスト更新',
            'business' => 'テスト更新',
            'number_of_employees' => 'テスト更新'
        ];

        $response = $this->actingAs($admin, "admin")->patch(route("admin.company.update", ["company" => $company->id]),$update_company);
        $this->assertDatabaseHas("companies", $update_company);
        $response->assertRedirect(route('admin.company.index'));
    }

}