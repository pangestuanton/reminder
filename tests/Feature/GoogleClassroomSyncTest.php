<?php

namespace Tests\Feature;

use App\Models\JadwalKegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleClassroomSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_multiple_users_can_have_tasks_from_the_same_classroom_course_work(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        // User A imports coursework
        $taskA = JadwalKegiatan::create([
            'user_id' => $userA->id,
            'judul' => 'Tugas Bersama ASD',
            'kategori' => 'tugas',
            'waktu_pelaksanaan' => now()->addDays(3),
            'source' => 'classroom',
            'source_id' => 'coursework-id-123',
            'status' => 'pending',
            'prioritas' => 'tinggi',
        ]);

        // User B imports the exact same coursework (with identical source & source_id)
        $taskB = JadwalKegiatan::create([
            'user_id' => $userB->id,
            'judul' => 'Tugas Bersama ASD',
            'kategori' => 'tugas',
            'waktu_pelaksanaan' => now()->addDays(3),
            'source' => 'classroom',
            'source_id' => 'coursework-id-123',
            'status' => 'pending',
            'prioritas' => 'tinggi',
        ]);

        $this->assertDatabaseHas('jadwal_kegiatans', [
            'id' => $taskA->id,
            'user_id' => $userA->id,
            'source_id' => 'coursework-id-123',
        ]);

        $this->assertDatabaseHas('jadwal_kegiatans', [
            'id' => $taskB->id,
            'user_id' => $userB->id,
            'source_id' => 'coursework-id-123',
        ]);
    }

    public function test_tasks_without_due_date_can_be_saved_with_null_waktu_pelaksanaan(): void
    {
        $user = User::factory()->create();

        $task = JadwalKegiatan::create([
            'user_id' => $user->id,
            'judul' => 'Tugas Tanpa Deadline',
            'kategori' => 'tugas',
            'waktu_pelaksanaan' => null,
            'source' => 'classroom',
            'source_id' => 'coursework-id-999',
            'status' => 'pending',
            'prioritas' => 'rendah',
        ]);

        $this->assertDatabaseHas('jadwal_kegiatans', [
            'id' => $task->id,
            'waktu_pelaksanaan' => null,
        ]);

        $this->assertNull($task->countdown_text);
    }
}
