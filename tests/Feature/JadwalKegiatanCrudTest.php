<?php

namespace Tests\Feature;

use App\Models\JadwalKegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalKegiatanCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_schedule(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('jadwal-kegiatan.store'), [
            'judul' => 'Tugas Jaringan',
            'kategori' => 'tugas',
            'waktu_pelaksanaan' => now()->addDay()->format('Y-m-d H:i:s'),
            'lokasi_atau_link' => 'Google Classroom',
            'deskripsi' => 'Kerjakan sebelum malam',
            'status' => 'pending',
            'prioritas' => 'tinggi',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('jadwal_kegiatans', [
            'user_id' => $user->id,
            'judul' => 'Tugas Jaringan',
        ]);
    }

    public function test_authenticated_user_can_update_schedule(): void
    {
        $user = User::factory()->create();
        $jadwal = JadwalKegiatan::factory()->for($user)->create();

        $response = $this->actingAs($user)->put(route('jadwal-kegiatan.update', $jadwal), [
            'judul' => 'Judul Baru',
            'kategori' => $jadwal->kategori,
            'waktu_pelaksanaan' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'lokasi_atau_link' => 'Ruang 101',
            'deskripsi' => 'Catatan baru',
            'status' => 'pending',
            'prioritas' => 'sedang',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('jadwal_kegiatans', ['id' => $jadwal->id, 'judul' => 'Judul Baru']);
    }

    public function test_authenticated_user_can_delete_schedule(): void
    {
        $user = User::factory()->create();
        $jadwal = JadwalKegiatan::factory()->for($user)->create();

        $response = $this->actingAs($user)->delete(route('jadwal-kegiatan.destroy', $jadwal));

        $response->assertRedirect(route('jadwal-kegiatan.index'));
        $this->assertDatabaseMissing('jadwal_kegiatans', ['id' => $jadwal->id]);
    }

    public function test_authenticated_user_can_mark_schedule_completed(): void
    {
        $user = User::factory()->create();
        $jadwal = JadwalKegiatan::factory()->for($user)->pending()->create();

        $response = $this->actingAs($user)->patch(route('jadwal-kegiatan.complete', $jadwal));

        $response->assertRedirect();
        $this->assertDatabaseHas('jadwal_kegiatans', ['id' => $jadwal->id, 'status' => 'selesai']);
    }

    public function test_user_can_search_and_filter_schedule(): void
    {
        $user = User::factory()->create();
        JadwalKegiatan::factory()->for($user)->create(['judul' => 'Kuliah Laravel', 'kategori' => 'kuliah']);
        JadwalKegiatan::factory()->for($user)->create(['judul' => 'Tugas Basis Data', 'kategori' => 'tugas']);

        $response = $this->actingAs($user)->get(route('jadwal-kegiatan.index', ['q' => 'Laravel', 'kategori' => 'kuliah']));

        $response->assertOk();
        $response->assertSee('Kuliah Laravel');
        $response->assertDontSee('Tugas Basis Data');
    }

    public function test_countdown_text_behavior(): void
    {
        $user = User::factory()->create();

        // 1. Completed task should have null countdown text
        $completedTask = JadwalKegiatan::factory()->for($user)->create([
            'waktu_pelaksanaan' => now()->addMinutes(30),
            'status' => 'selesai',
        ]);
        $this->assertNull($completedTask->countdown_text);

        // 2. Overdue pending task should say 'Tenggat sudah terlewat.'
        $overdueTask = JadwalKegiatan::factory()->for($user)->create([
            'waktu_pelaksanaan' => now()->subMinutes(30),
            'status' => 'pending',
        ]);
        $this->assertEquals('Tenggat sudah terlewat.', $overdueTask->countdown_text);

        // 3. Pending tugas due in 30 mins should say 'Tenggat dalam kurang dari 1 jam.'
        $tugasTask = JadwalKegiatan::factory()->for($user)->create([
            'waktu_pelaksanaan' => now()->addMinutes(30),
            'kategori' => 'tugas',
            'status' => 'pending',
        ]);
        $this->assertEquals('Tenggat dalam kurang dari 1 jam.', $tugasTask->countdown_text);

        // 4. Pending kuliah starting in 30 mins should say 'Dimulai dalam kurang dari 1 jam.'
        $kuliahTask = JadwalKegiatan::factory()->for($user)->create([
            'waktu_pelaksanaan' => now()->addMinutes(30),
            'kategori' => 'kuliah',
            'status' => 'pending',
        ]);
        $this->assertEquals('Dimulai dalam kurang dari 1 jam.', $kuliahTask->countdown_text);
    }
}

