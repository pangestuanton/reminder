<?php

namespace Tests\Feature;

use App\Models\JadwalKegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalKegiatanPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_view_another_users_schedule(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $jadwal = JadwalKegiatan::factory()->for($owner)->create();

        $response = $this->actingAs($other)->get(route('jadwal-kegiatan.show', $jadwal));

        $response->assertForbidden();
    }

    public function test_user_cannot_update_another_users_schedule(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $jadwal = JadwalKegiatan::factory()->for($owner)->create();

        $response = $this->actingAs($other)->put(route('jadwal-kegiatan.update', $jadwal), [
            'judul' => 'Tidak Boleh',
            'kategori' => 'tugas',
            'waktu_pelaksanaan' => now()->addDay()->format('Y-m-d H:i:s'),
            'lokasi_atau_link' => 'Ruang 1',
            'deskripsi' => 'Tes',
            'status' => 'pending',
            'prioritas' => 'sedang',
        ]);

        $response->assertForbidden();
    }

    public function test_user_cannot_delete_another_users_schedule(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $jadwal = JadwalKegiatan::factory()->for($owner)->create();

        $response = $this->actingAs($other)->delete(route('jadwal-kegiatan.destroy', $jadwal));

        $response->assertForbidden();
    }

    public function test_user_cannot_complete_another_users_schedule(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $jadwal = JadwalKegiatan::factory()->for($owner)->create();

        $response = $this->actingAs($other)->patch(route('jadwal-kegiatan.complete', $jadwal));

        $response->assertForbidden();
    }
}
