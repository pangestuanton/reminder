<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemePreferenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_saves_authenticated_theme_preference(): void
    {
        $user = User::factory()->create(['theme_preference' => 'system']);

        $this->actingAs($user)
            ->postJson(route('theme.update'), ['theme' => 'dark'])
            ->assertOk()
            ->assertJson(['success' => true, 'theme' => 'dark']);

        $this->assertEquals('dark', $user->fresh()->theme_preference);
    }

    public function test_validates_theme_preference_values(): void
    {
        $user = User::factory()->create(['theme_preference' => 'system']);

        $this->actingAs($user)
            ->postJson(route('theme.update'), ['theme' => 'invalid'])
            ->assertUnprocessable();

        $this->assertEquals('system', $user->fresh()->theme_preference);
    }

    public function test_requires_authentication_to_save_theme_preference(): void
    {
        $this->postJson(route('theme.update'), ['theme' => 'dark'])
            ->assertUnauthorized();
    }

    public function test_renders_initial_theme_script_with_authenticated_preference(): void
    {
        $user = User::factory()->create(['theme_preference' => 'dark']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee("localStorage.getItem('theme') || 'dark'", false)
            ->assertSee("document.documentElement.classList.add('dark')", false);
    }

    public function test_renders_system_mode_fallback_for_guests(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee("localStorage.getItem('theme') || 'system'", false)
            ->assertSee("prefers-color-scheme: dark", false);
    }
}
