<?php

namespace Tests\Feature;

use App\Models\JadwalKegiatan;
use App\Models\User;
use App\Notifications\ScheduleTelegramReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ReminderCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_h3_and_h1_reminders_are_sent_without_duplicates(): void
    {
        Notification::fake();

        $user = User::factory()->create(['telegram_chat_id' => '123456789']);
        JadwalKegiatan::factory()->for($user)->dueInDays(3)->create();
        JadwalKegiatan::factory()->for($user)->dueInDays(1)->create();

        $this->artisan('aviona:send-schedule-reminders')->assertSuccessful();
        $this->artisan('aviona:send-schedule-reminders')->assertSuccessful();

        $this->assertDatabaseCount('reminder_logs', 2);
        $this->assertDatabaseMissing('reminder_logs', ['status' => 'failed']);
        $this->assertDatabaseMissing('reminder_logs', ['channel' => 'mail']);
        $this->assertDatabaseMissing('reminder_logs', ['channel' => 'whatsapp']);
        Notification::assertSentToTimes($user, ScheduleTelegramReminder::class, 2);
    }

    public function test_completed_and_cancelled_schedules_do_not_receive_reminders(): void
    {
        Notification::fake();

        $user = User::factory()->create(['telegram_chat_id' => '123456789']);
        JadwalKegiatan::factory()->for($user)->selesai()->dueInDays(3)->create();
        JadwalKegiatan::factory()->for($user)->dibatalkan()->dueInDays(1)->create();

        $this->artisan('aviona:send-schedule-reminders')->assertSuccessful();

        $this->assertDatabaseCount('reminder_logs', 0);
    }

    public function test_3h_countdown_reminders_are_sent_every_30_minutes(): void
    {
        Notification::fake();

        $user = User::factory()->create(['telegram_chat_id' => '123456789']);

        $schedule = JadwalKegiatan::factory()->for($user)->create([
            'waktu_pelaksanaan' => now()->addMinutes(165),
            'status' => 'pending',
        ]);

        $this->artisan('aviona:send-schedule-reminders')->assertSuccessful();
        $this->assertDatabaseHas('reminder_logs', [
            'jadwal_kegiatan_id' => $schedule->id,
            'reminder_type' => '3h_slot_6',
            'channel' => 'telegram',
        ]);
        $this->assertDatabaseCount('reminder_logs', 1);

        $this->artisan('aviona:send-schedule-reminders')->assertSuccessful();
        $this->assertDatabaseCount('reminder_logs', 1);

        Carbon::setTestNow(now()->addMinutes(30));

        $this->artisan('aviona:send-schedule-reminders')->assertSuccessful();
        $this->assertDatabaseHas('reminder_logs', [
            'jadwal_kegiatan_id' => $schedule->id,
            'reminder_type' => '3h_slot_5',
            'channel' => 'telegram',
        ]);
        $this->assertDatabaseCount('reminder_logs', 2);

        Carbon::setTestNow();
    }
}
