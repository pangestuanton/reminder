<?php

namespace Tests\Unit;

use App\Models\CollegeSchedule;
use App\Models\JadwalKegiatan;
use App\Models\User;
use App\Services\TelegramMessageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TelegramMessageServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_escape_markdown_removes_special_chars(): void
    {
        $service = app(TelegramMessageService::class);

        $result = $service->escapeMarkdown('Hello *world* [test]');

        $this->assertStringContainsString('\*', $result);
        $this->assertStringContainsString('\[', $result);
        $this->assertStringContainsString('\]', $result);
    }

    public function test_build_reminder_message_contains_title(): void
    {
        $user = User::factory()->create();
        $jadwal = JadwalKegiatan::factory()->for($user)->create([
            'judul' => 'Tugas Pemrograman',
        ]);

        $service = app(TelegramMessageService::class);
        $message = $service->buildReminderMessage($jadwal, 'h3');

        $this->assertIsString($message);
        $this->assertNotEmpty($message);
    }

    public function test_build_college_class_message_contains_schedule_info(): void
    {
        $user = User::factory()->create();
        $schedule = CollegeSchedule::factory()->for($user)->create([
            'mata_kuliah' => 'Pemrograman Web',
            'dosen' => 'Dr. Budi',
            'lokasi' => 'Ruang A.1.01',
        ]);

        $service = app(TelegramMessageService::class);
        $message = $service->buildCollegeClassMessage($schedule);

        $this->assertStringContainsString('Pemrograman Web', $message);
        $this->assertStringContainsString('Dr\. Budi', $message);
    }
}
