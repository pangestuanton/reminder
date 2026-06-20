<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegrationsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_integrations_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('integrations.index'))->assertRedirect(route('profile.edit'));
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $this->get(route('integrations.index'))->assertRedirect(route('login'));
    }
}
