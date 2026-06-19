<?php

namespace App\Services;

use App\Models\JadwalKegiatan;
use App\Models\User;

class DashboardStatsService
{
    public function get(User $user): array
    {
        $baseQuery = JadwalKegiatan::query()->ownedBy($user);

        $total = (clone $baseQuery)->count();
        $pending = (clone $baseQuery)->where('status', 'pending')->count();
        $completed = (clone $baseQuery)->where('status', 'selesai')->count();
        $urgent = (clone $baseQuery)->dueWithinDays(7)->count();
        $nearest = (clone $baseQuery)->pending()->where('waktu_pelaksanaan', '>=', now())->orderBy('waktu_pelaksanaan')->first();
        $upcoming = (clone $baseQuery)->pending()->orderBy('waktu_pelaksanaan')->limit(5)->get();
        $urgentList = (clone $baseQuery)->dueWithinDays(7)->orderBy('waktu_pelaksanaan')->limit(5)->get();

        return compact('total', 'pending', 'completed', 'urgent', 'nearest', 'upcoming', 'urgentList');
    }

    public function countdownText(?JadwalKegiatan $jadwalKegiatan): ?string
    {
        if (! $jadwalKegiatan) {
            return null;
        }

        if ($jadwalKegiatan->isOverdue()) {
            return 'Tenggat sudah terlewat.';
        }

        $diffInMinutes = now()->diffInMinutes($jadwalKegiatan->waktu_pelaksanaan, false);

        if ($diffInMinutes < 60) {
            return 'Dimulai dalam kurang dari 1 jam.';
        }

        $diffInHours = now()->diffInHours($jadwalKegiatan->waktu_pelaksanaan, false);

        if ($diffInHours < 24) {
            return 'Tenggat dalam '.$diffInHours.' jam.';
        }

        $diffInDays = now()->diffInDays($jadwalKegiatan->waktu_pelaksanaan, false);

        return 'Tenggat dalam '.$diffInDays.' hari.';
    }
}
