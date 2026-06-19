<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoJadwalKegiatanSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'budi@akun.id'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password123'),
            ]
        );

        $schedules = [
            [
                'judul' => 'Kuliah Pemrograman Web',
                'kategori' => 'kuliah',
                'waktu_pelaksanaan' => now()->addDays(1)->setTime(8, 0),
                'lokasi_atau_link' => 'Ruang 301, Gedung Teknik',
                'deskripsi' => 'Materi: Laravel Authentication',
                'status' => 'pending',
                'prioritas' => 'tinggi',
            ],
            [
                'judul' => 'Pengumpulan Tugas Basis Data',
                'kategori' => 'tugas',
                'waktu_pelaksanaan' => now()->addDays(3)->setTime(23, 59),
                'lokasi_atau_link' => 'https://classroom.google.com',
                'deskripsi' => 'ERD dan Normalisasi',
                'status' => 'pending',
                'prioritas' => 'tinggi',
            ],
            [
                'judul' => 'UTS Algoritma dan Struktur Data',
                'kategori' => 'uts',
                'waktu_pelaksanaan' => now()->addDays(7)->setTime(9, 0),
                'lokasi_atau_link' => 'Lab Komputer 2',
                'deskripsi' => 'Materi: Sorting, Searching, Tree, Graph',
                'status' => 'pending',
                'prioritas' => 'tinggi',
            ],
            [
                'judul' => 'UAS Matematika Diskrit',
                'kategori' => 'uas',
                'waktu_pelaksanaan' => now()->addDays(21)->setTime(13, 0),
                'lokasi_atau_link' => 'Ruang 201, Gedung MIPA',
                'deskripsi' => '',
                'status' => 'pending',
                'prioritas' => 'sedang',
            ],
            [
                'judul' => 'Rapat Divisi PSDM',
                'kategori' => 'organisasi',
                'waktu_pelaksanaan' => now()->addDays(2)->setTime(16, 0),
                'lokasi_atau_link' => 'Ruang BEM lantai 3',
                'deskripsi' => 'Persiapan rekrutmen anggota baru',
                'status' => 'pending',
                'prioritas' => 'sedang',
            ],
            [
                'judul' => 'Kuliah Jaringan Komputer',
                'kategori' => 'kuliah',
                'waktu_pelaksanaan' => now()->addDays(4)->setTime(10, 0),
                'lokasi_atau_link' => 'Ruang 402, Gedung Teknik',
                'deskripsi' => 'Topik: TCP/IP dan OSI Model',
                'status' => 'pending',
                'prioritas' => 'sedang',
            ],
            [
                'judul' => 'Tugas Kelompok Rekayasa Perangkat Lunak',
                'kategori' => 'tugas',
                'waktu_pelaksanaan' => now()->subDay()->setTime(23, 59),
                'lokasi_atau_link' => 'https://classroom.google.com',
                'deskripsi' => 'Proposal Sistem Informasi',
                'status' => 'selesai',
                'prioritas' => 'rendah',
            ],
            [
                'judul' => 'Workshop Desain UI/UX',
                'kategori' => 'organisasi',
                'waktu_pelaksanaan' => now()->addDays(10)->setTime(14, 0),
                'lokasi_atau_link' => 'Aula Kampus',
                'deskripsi' => 'Workshop open untuk semua mahasiswa',
                'status' => 'pending',
                'prioritas' => 'rendah',
            ],
            [
                'judul' => 'Presentasi Tugas Besar Kecerdasan Buatan',
                'kategori' => 'tugas',
                'waktu_pelaksanaan' => now()->subDays(3)->setTime(10, 0),
                'lokasi_atau_link' => 'Ruang 301',
                'deskripsi' => 'Implementasi Neural Network',
                'status' => 'selesai',
                'prioritas' => 'tinggi',
            ],
            [
                'judul' => 'Kuliah Kewarganegaraan',
                'kategori' => 'kuliah',
                'waktu_pelaksanaan' => now()->subDays(5)->setTime(8, 0),
                'lokasi_atau_link' => 'Ruang 101',
                'deskripsi' => '',
                'status' => 'dibatalkan',
                'prioritas' => 'rendah',
            ],
        ];

        foreach ($schedules as $schedule) {
            $user->jadwalKegiatans()->create($schedule);
        }
    }
}
