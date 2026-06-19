<?php

namespace Tests\Unit;

use App\Models\JadwalKegiatan;
use App\Models\User;
use App\Services\ReminderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ReminderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_schedules_for_h3_and_h1_filters_pending_only(): void
    {
        Notification::fake();
        Http::fake(['*' => Http::response(['status' => true], 200)]);

        config()->set('services.fonnte.enabled', true);
        config()->set('services.fonnte.token', 'token-test');

        $user = User::factory()->create(['whatsapp_number' => '081234567890']);
        JadwalKegiatan::factory()->for($user)->dueInDays(3)->pending()->create();
        JadwalKegiatan::factory()->for($user)->dueInDays(1)->pending()->create();
        JadwalKegiatan::factory()->for($user)->dueInDays(3)->selesai()->create();

        $service = app(ReminderService::class);

        $this->assertCount(1, $service->getSchedulesForReminder(3, 'h3', 'mail'));
        $this->assertCount(1, $service->getSchedulesForReminder(1, 'h1', 'mail'));
    }
}
