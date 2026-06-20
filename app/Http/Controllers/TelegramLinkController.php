<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TelegramLinkController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $botUsername = ltrim((string) config('services.telegram.bot_username'), '@');

        if ($botUsername === '') {
            return back()->with('error', 'Bot Telegram belum dikonfigurasi oleh administrator.');
        }

        $token = Str::random(48);

        DB::transaction(function () use ($request, $token) {
            DB::table('telegram_link_tokens')
                ->where('user_id', $request->user()->id)
                ->delete();

            DB::table('telegram_link_tokens')->insert([
                'token_hash' => hash('sha256', $token),
                'user_id' => $request->user()->id,
                'expires_at' => now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->away("https://t.me/{$botUsername}?start={$token}");
    }

    public function destroy(Request $request): RedirectResponse
    {
        DB::table('telegram_link_tokens')
            ->where('user_id', $request->user()->id)
            ->delete();

        $request->user()->forceFill([
            'telegram_chat_id' => null,
            'telegram_linked_at' => null,
        ])->save();

        return back()->with('success', 'Akun Telegram berhasil dilepas.');
    }
}
