<?php

namespace Database\Factories;

use App\Models\CollegeSchedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CollegeScheduleFactory extends Factory
{
    protected $model = CollegeSchedule::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'mata_kuliah' => fake()->randomElement(['Pemrograman Web', 'Basis Data', 'Jaringan Komputer', 'Algoritma']),
            'dosen' => fake()->name(),
            'hari' => fake()->randomElement(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']),
            'jam_mulai' => fake()->randomElement(['08:00', '09:00', '10:00', '13:00']),
            'jam_selesai' => fake()->randomElement(['10:00', '11:00', '12:00', '15:00']),
            'lokasi' => fake()->randomElement(['Ruang A.1.01', 'Ruang B.2.03', 'Lab Komputer 1']),
            'catatan' => null,
            'warna' => fake()->randomElement(['#3B82F6', '#10B981', '#F59E0B', '#EF4444']),
            'reminder_minutes' => 30,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function forDay(string $day): static
    {
        return $this->state(['hari' => $day]);
    }
}
