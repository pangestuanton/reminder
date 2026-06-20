<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserNotificationPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_settings_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('settings.index'))->assertOk();
    }

    public function test_can_update_notification_preferences(): void
    {
        $user = User::factory()->create();
        UserNotificationPreference::create(['user_id' => $user->id]);

        $this->actingAs($user)->put(route('settings.notifications.update'), [
            'telegram_enabled' => false,
            'quiet_hours_start' => '23:00',
            'quiet_hours_end' => '07:00',
            'tone' => 'formal',
            'detail_level' => 'compact',
            'reminder_h3_enabled' => false,
            'reminder_h1_enabled' => true,
            'reminder_3h_enabled' => true,
            'reminder_overdue_enabled' => false,
            'reminder_max_per_day' => 10,
        ])->assertRedirect();

        $prefs = $user->fresh()->notificationPreference;
        $this->assertFalse($prefs->telegram_enabled);
        $this->assertEquals('formal', $prefs->tone);
        $this->assertEquals(10, $prefs->reminder_max_per_day);
    }

    public function test_can_update_agenda_settings(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('settings.agenda.update'), [
            'daily_agenda_enabled' => false,
            'daily_agenda_time' => '06:00',
            'daily_agenda_include_overdue' => false,
            'daily_agenda_format' => 'compact',
        ])->assertRedirect();

        $user->refresh();
        $this->assertFalse($user->daily_agenda_enabled);
        $this->assertEquals('06:00', $user->daily_agenda_time);
        $this->assertEquals('compact', $user->daily_agenda_format);
    }

    public function test_updating_preferences_with_omitted_checkboxes_sets_them_to_false(): void
    {
        $user = User::factory()->create();
        $prefs = UserNotificationPreference::create([
            'user_id' => $user->id,
            'telegram_enabled' => true,
            'reminder_h3_enabled' => true,
            'reminder_h1_enabled' => true,
            'reminder_3h_enabled' => true,
            'reminder_overdue_enabled' => true,
        ]);

        $this->actingAs($user)->put(route('settings.notifications.update'), [
            'quiet_hours_start' => '23:00',
            'quiet_hours_end' => '07:00',
            'tone' => 'formal',
            'detail_level' => 'compact',
            'reminder_max_per_day' => 10,
        ])->assertRedirect();

        $prefs->refresh();
        $this->assertFalse($prefs->telegram_enabled);
        $this->assertFalse($prefs->reminder_h3_enabled);
        $this->assertFalse($prefs->reminder_h1_enabled);
        $this->assertFalse($prefs->reminder_3h_enabled);
        $this->assertFalse($prefs->reminder_overdue_enabled);
    }

    public function test_updating_agenda_with_omitted_checkboxes_sets_them_to_false(): void
    {
        $user = User::factory()->create([
            'daily_agenda_enabled' => true,
            'daily_agenda_include_overdue' => true,
        ]);

        $this->actingAs($user)->put(route('settings.agenda.update'), [
            'daily_agenda_time' => '06:00',
            'daily_agenda_format' => 'compact',
        ])->assertRedirect();

        $user->refresh();
        $this->assertFalse($user->daily_agenda_enabled);
        $this->assertFalse($user->daily_agenda_include_overdue);
    }
}
