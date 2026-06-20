<?php

namespace Tests\Unit;

use App\Models\JadwalKegiatan;
use App\Models\User;
use App\Services\ReminderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReminderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_schedules_for_h3_and_h1_filters_pending_only(): void
    {
        $user = User::factory()->create(['telegram_chat_id' => '123456789']);
        JadwalKegiatan::factory()->for($user)->dueInDays(3)->pending()->create();
        JadwalKegiatan::factory()->for($user)->dueInDays(1)->pending()->create();
        JadwalKegiatan::factory()->for($user)->dueInDays(3)->selesai()->create();

        $service = app(ReminderService::class);

        $this->assertCount(1, $service->getSchedulesForReminder(3, 'h3'));
        $this->assertCount(1, $service->getSchedulesForReminder(1, 'h1'));
    }
}
