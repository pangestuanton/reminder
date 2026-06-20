<?php

namespace Database\Factories;

use App\Models\JadwalKegiatan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JadwalKegiatanFactory extends Factory
{
    protected $model = JadwalKegiatan::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'judul' => fake()->sentence(4),
            'kategori' => fake()->randomElement(['kuliah', 'tugas', 'uts', 'uas', 'organisasi']),
            'waktu_pelaksanaan' => fake()->dateTimeBetween('now', '+30 days'),
            'lokasi_atau_link' => fake()->optional(0.6)->sentence(3),
            'deskripsi' => fake()->optional(0.5)->paragraph(1),
            'status' => 'pending',
            'prioritas' => fake()->randomElement(['rendah', 'sedang', 'tinggi']),
            'source' => 'local',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function selesai(): static
    {
        return $this->state(fn () => ['status' => 'selesai']);
    }

    public function dibatalkan(): static
    {
        return $this->state(fn () => ['status' => 'dibatalkan']);
    }

    public function dueInDays(int $days): static
    {
        return $this->state(fn () => [
            'waktu_pelaksanaan' => now()->addDays($days),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'waktu_pelaksanaan' => now()->subDay(),
            'status' => 'pending',
        ]);
    }
}
