<?php

namespace Tests\Unit;

use App\Models\JadwalKegiatan;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_stats_returns_correct_counts(): void
    {
        $user = User::factory()->create();
        JadwalKegiatan::factory()->for($user)->pending()->count(5)->create();
        JadwalKegiatan::factory()->for($user)->selesai()->count(3)->create();

        $stats = app(AnalyticsService::class)->getStats($user);

        $this->assertEquals(8, $stats['total']);
        $this->assertEquals(3, $stats['completed']);
        $this->assertEquals(5, $stats['in_progress']);
        $this->assertEquals(37.5, $stats['percentage']);
    }

    public function test_get_stats_excludes_informational_items(): void
    {
        $user = User::factory()->create();
        JadwalKegiatan::factory()->for($user)->pending()->count(2)->create();

        $stats = app(AnalyticsService::class)->getStats($user);

        $this->assertEquals(2, $stats['total']);
    }

    public function test_get_stats_can_filter_by_category(): void
    {
        $user = User::factory()->create();
        JadwalKegiatan::factory()->for($user)->create(['kategori' => 'tugas']);
        JadwalKegiatan::factory()->for($user)->create(['kategori' => 'uts']);

        $stats = app(AnalyticsService::class)->getStats($user, null, null, null, 'tugas');

        $this->assertEquals(1, $stats['total']);
    }

    public function test_get_stats_can_filter_by_source(): void
    {
        $user = User::factory()->create();
        JadwalKegiatan::factory()->for($user)->create(['source' => 'local']);
        JadwalKegiatan::factory()->for($user)->create(['source' => 'classroom', 'source_id' => 'ext_123']);

        $stats = app(AnalyticsService::class)->getStats($user, null, null, null, null, 'classroom');

        $this->assertEquals(1, $stats['total']);
    }

    public function test_weekly_trend_returns_7_days(): void
    {
        $user = User::factory()->create();

        $stats = app(AnalyticsService::class)->getStats($user);

        $this->assertCount(7, $stats['weekly_trend']);
    }

    public function test_get_user_courses_returns_unique_names(): void
    {
        $user = User::factory()->create();
        JadwalKegiatan::factory()->for($user)->create(['course_name' => 'Pemrograman Web']);
        JadwalKegiatan::factory()->for($user)->create(['course_name' => 'Pemrograman Web']);
        JadwalKegiatan::factory()->for($user)->create(['course_name' => 'Basis Data']);

        $courses = app(AnalyticsService::class)->getUserCourses($user);

        $this->assertCount(2, $courses);
    }
}
