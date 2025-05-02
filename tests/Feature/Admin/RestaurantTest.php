<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RestaurantTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_guest_can_not_access_admin_restaurants_index(): void
    {
        $response = $this->get(route("admin.restaurants.index"));

        $response->assertRedirect(route("admin/auth/login"));
    }
}
