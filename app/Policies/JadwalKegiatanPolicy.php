<?php

namespace App\Policies;

use App\Models\JadwalKegiatan;
use App\Models\User;

class JadwalKegiatanPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, JadwalKegiatan $jadwalKegiatan): bool
    {
        return $user->id === $jadwalKegiatan->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, JadwalKegiatan $jadwalKegiatan): bool
    {
        return $user->id === $jadwalKegiatan->user_id;
    }

    public function delete(User $user, JadwalKegiatan $jadwalKegiatan): bool
    {
        return $user->id === $jadwalKegiatan->user_id;
    }

    public function complete(User $user, JadwalKegiatan $jadwalKegiatan): bool
    {
        return $user->id === $jadwalKegiatan->user_id;
    }
}
