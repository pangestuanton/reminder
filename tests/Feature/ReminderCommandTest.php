<?php

namespace Tests\Feature;

use App\Models\JadwalKegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ReminderCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_h3_and_h1_reminders_are_sent_without_duplicates(): void
    {
        Notification::fake();
        Http::fake(['*' => Http::response(['status' => true], 200)]);

        config()->set('services.fonnte.enabled', true);
        config()->set('services.fonnte.token', 'token-test');

        $user = User::factory()->create(['whatsapp_number' => '081234567890']);
        JadwalKegiatan::factory()->for($user)->dueInDays(3)->create();
        JadwalKegiatan::factory()->for($user)->dueInDays(1)->create();

        $this->artisan('aviona:send-schedule-reminders')->assertSuccessful();
        $this->artisan('aviona:send-schedule-reminders')->assertSuccessful();

        $this->assertDatabaseCount('reminder_logs', 4);
    }

    public function test_completed_and_cancelled_schedules_do_not_receive_reminders(): void
    {
        Notification::fake();
        Http::fake(['*' => Http::response(['status' => true], 200)]);

        config()->set('services.fonnte.enabled', true);
        config()->set('services.fonnte.token', 'token-test');

        $user = User::factory()->create(['whatsapp_number' => '081234567890']);
        JadwalKegiatan::factory()->for($user)->selesai()->dueInDays(3)->create();
        JadwalKegiatan::factory()->for($user)->dibatalkan()->dueInDays(1)->create();

        $this->artisan('aviona:send-schedule-reminders')->assertSuccessful();

        $this->assertDatabaseCount('reminder_logs', 0);
    }
}
