<?php

namespace Tests\Unit;

use App\Models\JadwalKegiatan;
use App\Models\User;
use App\Services\DashboardStatsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_counts_and_nearest_schedule_are_correct(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        JadwalKegiatan::factory()->for($user)->pending()->dueInDays(2)->create();
        JadwalKegiatan::factory()->for($user)->pending()->dueInDays(5)->create();
        JadwalKegiatan::factory()->for($user)->selesai()->create();
        JadwalKegiatan::factory()->for($other)->pending()->dueInDays(1)->create();

        $stats = app(DashboardStatsService::class)->get($user);

        $this->assertSame(3, $stats['total']);
        $this->assertSame(2, $stats['pending']);
        $this->assertSame(1, $stats['completed']);
        $this->assertNotNull($stats['nearest']);
        $this->assertSame($user->id, $stats['nearest']->user_id);
    }
}
