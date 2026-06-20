<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJadwalKegiatanRequest;
use App\Http\Requests\UpdateJadwalKegiatanRequest;
use App\Models\JadwalKegiatan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalKegiatanController extends Controller
{
    public function __construct() {}

    public function index(Request $request): View
    {
        $query = JadwalKegiatan::query()
            ->ownedBy($request->user())
            ->search($request->string('q')->toString())
            ->category($request->string('kategori')->toString())
            ->status($request->string('status')->toString())
            ->priority($request->string('prioritas')->toString());

        if ($request->filled('tanggal_mulai')) {
            $query->where('waktu_pelaksanaan', '>=', $request->input('tanggal_mulai'));
        }

        if ($request->filled('tanggal_selesai')) {
            $query->where('waktu_pelaksanaan', '<=', $request->input('tanggal_selesai').' 23:59:59');
        }

        match ($request->input('sort', 'nearest')) {
            'oldest' => $query->orderBy('waktu_pelaksanaan'),
            'newest' => $query->orderByDesc('created_at'),
            'priority' => $query->orderByRaw("CASE prioritas WHEN 'tinggi' THEN 1 WHEN 'sedang' THEN 2 ELSE 3 END")
                ->orderBy('waktu_pelaksanaan'),
            default => $query->orderBy('waktu_pelaksanaan'),
        };

        return view('jadwal-kegiatan.index', [
            'jadwalKegiatans' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['q', 'kategori', 'status', 'prioritas', 'tanggal_mulai', 'tanggal_selesai', 'sort']),
            'categories' => ['kuliah', 'tugas', 'uts', 'uas', 'organisasi'],
            'statuses' => ['pending', 'selesai', 'dibatalkan'],
            'priorities' => ['rendah', 'sedang', 'tinggi'],
        ]);
    }

    public function create(): View
    {
        return view('jadwal-kegiatan.create', [
            'jadwalKegiatan' => new JadwalKegiatan(['status' => 'pending', 'prioritas' => 'sedang']),
        ]);
    }

    public function store(StoreJadwalKegiatanRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $jadwal = JadwalKegiatan::create($data);

        return redirect()->route('jadwal-kegiatan.show', $jadwal)->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function show(JadwalKegiatan $jadwalKegiatan): View
    {
        return view('jadwal-kegiatan.show', compact('jadwalKegiatan'));
    }

    public function edit(JadwalKegiatan $jadwalKegiatan): View
    {
        return view('jadwal-kegiatan.edit', compact('jadwalKegiatan'));
    }

    public function update(UpdateJadwalKegiatanRequest $request, JadwalKegiatan $jadwalKegiatan): RedirectResponse
    {
        $jadwalKegiatan->update($request->validated());

        return redirect()->route('jadwal-kegiatan.show', $jadwalKegiatan)->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(JadwalKegiatan $jadwalKegiatan): RedirectResponse
    {
        $jadwalKegiatan->delete();

        return redirect()->route('jadwal-kegiatan.index')->with('success', 'Jadwal berhasil dihapus.');
    }

    public function complete(JadwalKegiatan $jadwalKegiatan): RedirectResponse
    {
        $this->authorize('complete', $jadwalKegiatan);

        $jadwalKegiatan->update([
            'status' => 'selesai',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Tugas berhasil ditandai selesai.');
    }
}
