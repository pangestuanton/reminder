<?php

namespace Tests\Feature;

use App\Jobs\SendDailyAgendaJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DailyAgendaTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_agenda_job_is_queued_for_eligible_users(): void
    {
        Queue::fake();

        $user = User::factory()->create([
            'daily_agenda_enabled' => true,
            'telegram_chat_id' => '123456789',
        ]);

        SendDailyAgendaJob::dispatch($user);

        Queue::assertPushed(SendDailyAgendaJob::class, 1);
    }

    public function test_daily_agenda_job_is_not_queued_when_disabled(): void
    {
        Queue::fake();

        $user = User::factory()->create([
            'daily_agenda_enabled' => false,
            'telegram_chat_id' => '123456789',
        ]);

        SendDailyAgendaJob::dispatch($user);

        Queue::assertPushed(SendDailyAgendaJob::class, 1);
    }

    public function test_daily_agenda_command_dispatches_to_users(): void
    {
        Queue::fake();

        User::factory()->count(3)->sequence(
            ['telegram_chat_id' => '123456781'],
            ['telegram_chat_id' => '123456782'],
            ['telegram_chat_id' => '123456783'],
        )->create([
            'daily_agenda_enabled' => true,
        ]);

        $this->artisan('aviona:send-daily-agenda')->assertSuccessful();

        Queue::assertPushed(SendDailyAgendaJob::class, 3);
    }
}
