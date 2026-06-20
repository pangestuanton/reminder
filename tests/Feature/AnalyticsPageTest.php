<?php

namespace Tests\Feature;

use App\Models\JadwalKegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_analytics_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('academic-tracker.index', ['tab' => 'analitik']))->assertOk();
    }

    public function test_analytics_shows_stats(): void
    {
        $user = User::factory()->create();
        JadwalKegiatan::factory()->for($user)->pending()->count(3)->create();
        JadwalKegiatan::factory()->for($user)->selesai()->create();

        $response = $this->actingAs($user)->get(route('academic-tracker.index', ['tab' => 'analitik']));
        $response->assertOk();
        $response->assertSee('4');
        $response->assertSee('25');
    }

    public function test_analytics_can_filter_by_category(): void
    {
        $user = User::factory()->create();
        JadwalKegiatan::factory()->for($user)->create(['kategori' => 'tugas']);
        JadwalKegiatan::factory()->for($user)->create(['kategori' => 'uts']);

        $response = $this->actingAs($user)->get(route('academic-tracker.index', ['tab' => 'analitik', 'category' => 'tugas']));
        $response->assertOk();
        $response->assertSee('50');
    }
}
