<?php

namespace App\Http\Controllers;

use App\Models\UserNotificationPreference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $prefs = $user->notificationPreference;

        if (! $prefs) {
            $prefs = UserNotificationPreference::create(['user_id' => $user->id]);
        }

        return view('settings.index', ['preferences' => $prefs, 'user' => $user]);
    }

    public function updateNotification(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'telegram_enabled' => 'nullable|boolean',
            'quiet_hours_start' => 'required|string',
            'quiet_hours_end' => 'required|string',
            'tone' => 'required|string|in:friendly,formal,casual',
            'detail_level' => 'required|string|in:compact,normal,detailed',
            'reminder_h3_enabled' => 'nullable|boolean',
            'reminder_h1_enabled' => 'nullable|boolean',
            'reminder_3h_enabled' => 'nullable|boolean',
            'reminder_overdue_enabled' => 'nullable|boolean',
            'reminder_max_per_day' => 'required|integer|min:1|max:50',
        ]);

        $user = $request->user();

        $prefs = UserNotificationPreference::firstOrCreate(
            ['user_id' => $user->id],
            ['user_id' => $user->id]
        );

        $prefs->update([
            'telegram_enabled' => $request->boolean('telegram_enabled'),
            'quiet_hours_start' => $data['quiet_hours_start'],
            'quiet_hours_end' => $data['quiet_hours_end'],
            'tone' => $data['tone'],
            'detail_level' => $data['detail_level'],
            'reminder_h3_enabled' => $request->boolean('reminder_h3_enabled'),
            'reminder_h1_enabled' => $request->boolean('reminder_h1_enabled'),
            'reminder_3h_enabled' => $request->boolean('reminder_3h_enabled'),
            'reminder_overdue_enabled' => $request->boolean('reminder_overdue_enabled'),
            'reminder_max_per_day' => $data['reminder_max_per_day'],
        ]);

        return back()->with('success', 'Pengaturan notifikasi berhasil diperbarui.');
    }

    public function updateAgenda(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'daily_agenda_enabled' => 'nullable|boolean',
            'daily_agenda_time' => 'required|string',
            'daily_agenda_include_overdue' => 'nullable|boolean',
            'daily_agenda_format' => 'required|string|in:compact,detailed',
        ]);

        $request->user()->update([
            'daily_agenda_enabled' => $request->boolean('daily_agenda_enabled'),
            'daily_agenda_time' => $data['daily_agenda_time'],
            'daily_agenda_include_overdue' => $request->boolean('daily_agenda_include_overdue'),
            'daily_agenda_format' => $data['daily_agenda_format'],
        ]);

        return back()->with('success', 'Pengaturan agenda harian berhasil diperbarui.');
    }
}
