<?php

namespace Tests\Feature;

use App\Models\JadwalKegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_statistics(): void
    {
        $user = User::factory()->create();

        JadwalKegiatan::factory()->for($user)->count(2)->pending()->create();
        JadwalKegiatan::factory()->for($user)->selesai()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Menunggu');
        $response->assertSee('Selesai');
        $response->assertSee('3', false);
    }
}
