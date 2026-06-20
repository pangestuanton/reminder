<?php

namespace App\Http\Controllers;

use App\Jobs\ExportToGoogleCalendarJob;
use App\Jobs\SyncGoogleCalendarJob;
use App\Models\GoogleCalendarEvent;
use App\Models\JadwalKegiatan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $events = GoogleCalendarEvent::ownedBy($user)
            ->orderBy('start_datetime')
            ->get();

        $jadwalItems = JadwalKegiatan::ownedBy($user)
            ->where('status', '!=', 'dibatalkan')
            ->where('waktu_pelaksanaan', '>=', now()->subDays(7))
            ->where('waktu_pelaksanaan', '<=', now()->addDays(30))
            ->orderBy('waktu_pelaksanaan')
            ->get();

        return view('calendar.index', compact('events', 'jadwalItems'));
    }

    public function sync(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasCalendarAccess()) {
            return back()->with('error', 'Google Calendar belum terhubung.');
        }

        SyncGoogleCalendarJob::dispatch($user);

        return back()->with('success', 'Sinkronisasi Calendar dijadwalkan.');
    }

    public function exportTask(Request $request, JadwalKegiatan $jadwal): RedirectResponse
    {
        abort_unless($jadwal->user_id === $request->user()->id, 403);

        if (! $request->user()->hasCalendarAccess()) {
            return back()->with('error', 'Google Calendar belum terhubung.');
        }

        ExportToGoogleCalendarJob::dispatch($request->user(), 'jadwal', $jadwal->id);

        return back()->with('success', 'Ekspor tugas ke Calendar dijadwalkan.');
    }

    public function disconnect(Request $request): RedirectResponse
    {
        $user = $request->user();
        $account = $user->googleAccount;

        if ($account) {
            $account->update(['calendar_connected_at' => null]);
            GoogleCalendarEvent::where('user_id', $user->id)->delete();
        }

        return back()->with('success', 'Google Calendar berhasil diputuskan.');
    }
}
