<?php

namespace Tests\Feature;

use App\Models\CollegeSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollegeScheduleCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_college_schedule_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('college-schedule.index'))->assertOk();
    }

    public function test_can_create_college_schedule(): void
    {
        $user = User::factory()->create();

        $data = [
            'mata_kuliah' => 'Pemrograman Web',
            'dosen' => 'Dr. Budi',
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'lokasi' => 'Ruang A.1.01',
            'warna' => '#3B82F6',
        ];

        $this->actingAs($user)->post(route('college-schedule.store'), $data)
            ->assertRedirect();

        $this->assertDatabaseHas('college_schedules', [
            'user_id' => $user->id,
            'mata_kuliah' => 'Pemrograman Web',
            'hari' => 'Senin',
        ]);
    }

    public function test_can_update_college_schedule(): void
    {
        $user = User::factory()->create();
        $schedule = CollegeSchedule::factory()->for($user)->create();

        $this->actingAs($user)->put(route('college-schedule.update', $schedule), [
            'mata_kuliah' => 'Pemrograman Web Lanjutan',
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
        ])->assertRedirect();

        $this->assertDatabaseHas('college_schedules', [
            'id' => $schedule->id,
            'mata_kuliah' => 'Pemrograman Web Lanjutan',
        ]);
    }

    public function test_can_delete_college_schedule(): void
    {
        $user = User::factory()->create();
        $schedule = CollegeSchedule::factory()->for($user)->create();

        $this->actingAs($user)->delete(route('college-schedule.destroy', $schedule))
            ->assertRedirect();

        $this->assertDatabaseMissing('college_schedules', ['id' => $schedule->id]);
    }

    public function test_can_toggle_college_schedule(): void
    {
        $user = User::factory()->create();
        $schedule = CollegeSchedule::factory()->for($user)->create(['is_active' => true]);

        $this->actingAs($user)->patch(route('college-schedule.toggle', $schedule))
            ->assertRedirect();

        $schedule->refresh();
        $this->assertFalse($schedule->is_active);
    }

    public function test_cannot_manage_other_users_schedule(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $schedule = CollegeSchedule::factory()->for($other)->create();

        $this->actingAs($user)->get(route('college-schedule.show', $schedule))->assertForbidden();
        $this->actingAs($user)->delete(route('college-schedule.destroy', $schedule))->assertForbidden();
    }

    public function test_validation_requires_mata_kuliah(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('college-schedule.store'), [
            'mata_kuliah' => '',
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
        ])->assertSessionHasErrors('mata_kuliah');
    }

    public function test_validation_requires_valid_hari(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('college-schedule.store'), [
            'mata_kuliah' => 'Pemrograman Web',
            'hari' => 'InvalidDay',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
        ])->assertSessionHasErrors('hari');
    }
}
