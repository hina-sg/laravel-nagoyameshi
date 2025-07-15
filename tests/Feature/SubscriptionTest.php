<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Admin;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_not_access_subscription_create(): void
    {
        $response = $this->get(route("subscription.create"));

        $response->assertRedirect(route("login"));
    }

    public function test_free_user_can_access_subscription_create(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->get(route("subscription.create"));

        $response->assertOK();
    }

    public function test_subscribed_user_can_not_access_subscription_create(): void
    {
        $user = User::factory()->create();
        $user->newSubscription("premium_plan", env("STRIPE_PREMIUM_PLAN_PRICE_ID"))->create("pm_card_visa");
        $response = $this->actingAs($user, "web")->get(route("subscription.create"));

        $response->assertRedirect(route("subscription.edit"));
    }

    public function test_admin_can_not_access_subscription_create(): void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $response = $this->actingAs($admin, "admin")->get(route("subscription.create"));

        $response->assertRedirect(route("login"));
    }


        public function test_guest_can_not_access_subscription_store(): void
    {
        $response = $this->post(route("subscription.store"));

        $response->assertRedirect(route("login"));
    }

    public function test_free_user_can_access_subscription_store(): void
    {
        $user = User::factory()->create();
        $request_parameter = [
    'paymentMethodId' => 'pm_card_visa'
];
        $response = $this->actingAs($user, "web")->post(route("subscription.store"), $request_parameter);

        $response->assertRedirect(route("home"));

        $user->refresh();
        $this->assertTrue($user->subscribed("premium_plan"));
    }

    public function test_subscribed_user_can_not_access_subscription_store(): void
    {
        $user = User::factory()->create();
        $request_parameter = [
    'paymentMethodId' => 'pm_card_visa'
];
        $user->newSubscription("premium_plan", env("STRIPE_PREMIUM_PLAN_PRICE_ID"))->create("pm_card_visa");
        $response = $this->actingAs($user, "web")->post(route("subscription.store"), $request_parameter);

        $response->assertStatus(302);
    }

    public function test_admin_can_not_access_subscription_store(): void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $response = $this->actingAs($admin, "admin")->post(route("subscription.store"));

        $response->assertRedirect(route("login"));
    }


        public function test_guest_can_not_access_subscription_edit(): void
    {
        $response = $this->get(route("subscription.edit"));

        $response->assertRedirect(route("login"));
    }

    public function test_free_user_can_not_access_subscription_edit(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->get(route("subscription.edit"));

        $response->assertRedirect(route("subscription.create"));
    }

    public function test_subscribed_user_can_access_subscription_edit(): void
    {
        $user = User::factory()->create();
        $user->newSubscription("premium_plan", env("STRIPE_PREMIUM_PLAN_PRICE_ID"))->create("pm_card_visa");

        $response = $this->actingAs($user, "web")->get(route("subscription.edit"));

        $response->assertOK();
    }

    public function test_admin_can_not_access_subscription_edit(): void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $response = $this->actingAs($admin, "admin")->get(route("subscription.edit"));

        $response->assertRedirect(route("login"));
    }


    public function test_guest_can_not_access_subscription_update(): void
    {
        $response = $this->put(route("subscription.update"));

        $response->assertRedirect(route("login"));
    }

    public function test_free_user_can_not_access_subscription_update(): void
    {
        $user = User::factory()->create();
        $request_parameter = [
        'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->actingAs($user, "web")->put(route("subscription.update"), $request_parameter);

        $response->assertStatus(302);

    }

    public function test_subscribed_user_can_access_subscription_update(): void
    {
        $user = User::factory()->create();
        $user->newSubscription("premium_plan", env("STRIPE_PREMIUM_PLAN_PRICE_ID"))->create("pm_card_visa");
        $default_payment_method_id = $user->defaultPaymentMethod()->id;
        
        $request_parameter = [
        'paymentMethodId' => 'pm_card_mastercard'
        ];
        $response = $this->actingAs($user, "web")->put(route("subscription.update"), $request_parameter);

        $response->assertRedirect(route("home"));

        $user->refresh();
        $default_payment_method_id2 = $user->defaultPaymentMethod()->id;
        $this->assertNotEquals($default_payment_method_id, $default_payment_method_id2);

    }

    public function test_admin_can_not_access_subscription_update(): void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $response = $this->actingAs($admin, "admin")->put(route("subscription.update"));

        $response->assertRedirect(route("login"));
    }


    public function test_guest_can_not_access_subscription_cancel(): void
    {
        $response = $this->get(route("subscription.cancel"));

        $response->assertRedirect(route("login"));
    }

    public function test_free_user_can_not_access_subscription_cancel(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, "web")->get(route("subscription.cancel"));

        $response->assertRedirect(route("subscription.create"));
    }

    public function test_subscribed_user_can_access_subscription_cancel(): void
    {
        $user = User::factory()->create();
        $user->newSubscription("premium_plan", env("STRIPE_PREMIUM_PLAN_PRICE_ID"))->create("pm_card_visa");

        $response = $this->actingAs($user, "web")->get(route("subscription.cancel"));

        $response->assertOK();
    }

    public function test_admin_can_not_access_subscription_cancel(): void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $response = $this->actingAs($admin, "admin")->get(route("subscription.cancel"));

        $response->assertRedirect(route("login"));
    }


    public function test_guest_can_not_access_subscription_destroy(): void
    {
        $response = $this->delete(route("subscription.destroy"));

        $response->assertRedirect(route("login"));
    }

    public function test_free_user_can_not_access_subscription_destroy(): void
    {
        $user = User::factory()->create();
        $request_parameter = [
        'paymentMethodId' => 'pm_card_visa'
        ];
        $response = $this->actingAs($user, "web")->delete(route("subscription.destroy"), $request_parameter);

        $response->assertStatus(302);
    }

    public function test_subscribed_user_can_access_subscription_destroy(): void
    {
        $user = User::factory()->create();
        $user->newSubscription("premium_plan", env("STRIPE_PREMIUM_PLAN_PRICE_ID"))->create("pm_card_visa");        
        $request_parameter = [
        'paymentMethodId' => 'pm_card_mastercard'
        ];
        $response = $this->actingAs($user, "web")->delete(route("subscription.destroy"), $request_parameter);

        $response->assertRedirect(route("home"));

        $user->refresh();
        $this->assertFalse($user->subscribed("premium_plan"));

    }

    public function test_admin_can_not_access_subscription_destroy(): void
    {
        $admin = new Admin();
        $admin->email = "admin@example.com";
        $admin->password = Hash::make("nagoyameshi");
        $admin->save();

        $response = $this->actingAs($admin, "admin")->delete(route("subscription.destroy"));

        $response->assertRedirect(route("login"));
    }
}
