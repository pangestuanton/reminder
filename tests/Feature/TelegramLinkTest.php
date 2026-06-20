<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\TelegramAccountLinked;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TelegramLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_start_telegram_linking(): void
    {
        config()->set('services.telegram.bot_username', 'aviona_test_bot');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('profile.telegram.store'));

        $response->assertRedirect();
        $this->assertStringStartsWith('https://t.me/aviona_test_bot?start=', $response->headers->get('Location'));
        $this->assertDatabaseHas('telegram_link_tokens', ['user_id' => $user->id]);
    }

    public function test_webhook_links_telegram_account_using_one_time_token(): void
    {
        Notification::fake();
        config()->set('services.telegram.webhook_secret', 'webhook-secret-test');

        $user = User::factory()->create();
        $plainToken = 'valid-link-token';

        DB::table('telegram_link_tokens')->insert([
            'token_hash' => hash('sha256', $plainToken),
            'user_id' => $user->id,
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->postJson('/api/telegram/webhook', [
            'message' => [
                'text' => '/start '.$plainToken,
                'chat' => ['id' => 123456789, 'type' => 'private'],
            ],
        ], [
            'X-Telegram-Bot-Api-Secret-Token' => 'webhook-secret-test',
        ])->assertNoContent();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'telegram_chat_id' => '123456789',
        ]);
        $this->assertDatabaseMissing('telegram_link_tokens', [
            'token_hash' => hash('sha256', $plainToken),
            'used_at' => null,
        ]);
        Notification::assertSentOnDemand(TelegramAccountLinked::class);
    }

    public function test_webhook_rejects_invalid_secret(): void
    {
        config()->set('services.telegram.webhook_secret', 'correct-secret');

        $this->postJson('/api/telegram/webhook', [], [
            'X-Telegram-Bot-Api-Secret-Token' => 'wrong-secret',
        ])->assertForbidden();
    }

    public function test_user_can_disconnect_telegram(): void
    {
        $user = User::factory()->create([
            'telegram_chat_id' => '123456789',
            'telegram_linked_at' => now(),
        ]);

        $this->actingAs($user)
            ->delete(route('profile.telegram.destroy'))
            ->assertRedirect();

        $this->assertNull($user->fresh()->telegram_chat_id);
        $this->assertNull($user->fresh()->telegram_linked_at);
    }
}
