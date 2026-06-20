<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCollegeScheduleRequest;
use App\Http\Requests\UpdateCollegeScheduleRequest;
use App\Models\CollegeSchedule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CollegeScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $schedules = CollegeSchedule::ownedBy($request->user())
            ->active()
            ->currentSemester()
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $grouped = $schedules->groupBy('hari');

        return view('college-schedule.index', compact('schedules', 'grouped', 'days'));
    }

    public function create(): View
    {
        return view('college-schedule.create');
    }

    public function store(StoreCollegeScheduleRequest $request): RedirectResponse
    {
        $schedule = CollegeSchedule::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('college-schedule.show', $schedule)
            ->with('success', 'Jadwal kuliah berhasil ditambahkan.');
    }

    public function show(Request $request, CollegeSchedule $collegeSchedule): View
    {
        abort_unless($collegeSchedule->user_id === $request->user()->id, 403);

        return view('college-schedule.show', ['schedule' => $collegeSchedule]);
    }

    public function edit(Request $request, CollegeSchedule $collegeSchedule): View
    {
        abort_unless($collegeSchedule->user_id === $request->user()->id, 403);

        return view('college-schedule.edit', ['schedule' => $collegeSchedule]);
    }

    public function update(UpdateCollegeScheduleRequest $request, CollegeSchedule $collegeSchedule): RedirectResponse
    {
        abort_unless($collegeSchedule->user_id === $request->user()->id, 403);

        $collegeSchedule->update($request->validated());

        return redirect()->route('college-schedule.show', $collegeSchedule)
            ->with('success', 'Jadwal kuliah berhasil diperbarui.');
    }

    public function destroy(Request $request, CollegeSchedule $collegeSchedule): RedirectResponse
    {
        abort_unless($collegeSchedule->user_id === $request->user()->id, 403);

        $collegeSchedule->delete();

        return redirect()->route('college-schedule.index')
            ->with('success', 'Jadwal kuliah berhasil dihapus.');
    }

    public function toggle(Request $request, CollegeSchedule $collegeSchedule): RedirectResponse
    {
        abort_unless($collegeSchedule->user_id === $request->user()->id, 403);

        $collegeSchedule->update(['is_active' => ! $collegeSchedule->is_active]);

        $status = $collegeSchedule->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Jadwal kuliah berhasil {$status}.");
    }

    public function downloadPdf(Request $request): \Illuminate\Http\Response
    {
        $schedules = CollegeSchedule::ownedBy($request->user())
            ->active()
            ->currentSemester()
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $grouped = $schedules->groupBy('hari');

        $pdf = Pdf::loadView('college-schedule.pdf', compact('schedules', 'grouped', 'days'));

        return $pdf->download('jadwal-kuliah.pdf');
    }
}
